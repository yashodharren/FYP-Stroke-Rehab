<?php

namespace App\Http\Controllers\Clinician;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\RehabPlan;
use App\Models\Exercise;
use App\Models\PlanExercise;
use App\Services\MLPredictionService;
use Illuminate\Http\Request;

class PlanGeneratorController extends Controller
{
    public function indexPlans()
    {
        $clinician = auth()->user();
        $rehabPlans = RehabPlan::where('clinician_id', $clinician->id)->with('patient')->get();

        return view('clinician.plans.index', ['rehabPlans' => $rehabPlans]);
    }

    public function create($patientId)
    {
        $clinician = auth()->user();
        $patient = Patient::findOrFail($patientId);

        if ($patient->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized access to this patient.');
        }

        $mlService = new MLPredictionService();
        $mlAvailable = $mlService->isServiceAvailable();
        $mlPrediction = null;
        $mlError = null;

        if ($mlAvailable) {
            try {
                // Prepare IST clinical data for ML prediction
                $clinicalData = [
                    'age' => $patient->age,
                    'gender' => $patient->gender,
                    'rsbp' => $patient->rsbp,
                    'stroke_subtype' => $patient->stroke_subtype,
                    'conscious_state' => $patient->conscious_state,
                    'rdef1' => (bool) $patient->rdef1,
                    'rdef2' => (bool) $patient->rdef2,
                    'rdef3' => (bool) $patient->rdef3,
                    'rdef4' => (bool) $patient->rdef4,
                    'rdef5' => (bool) $patient->rdef5,
                    'rdef6' => (bool) $patient->rdef6,
                    'rdef7' => (bool) $patient->rdef7,
                    'rdef8' => (bool) $patient->rdef8,
                ];

                $mlPrediction = $mlService->predictRecoveryWithISTData($clinicalData);
            } catch (\Exception $e) {
                \Log::warning('ML prediction failed: ' . $e->getMessage());
                $mlError = $e->getMessage();
            }
        }

        return view('clinician.plans.create', [
            'patient' => $patient,
            'mlPrediction' => $mlPrediction,
            'mlAvailable' => $mlAvailable,
            'mlError' => $mlError,
        ]);
    }

    public function store(Request $request, $patientId)
    {
        $clinician = auth()->user();
        $patient = Patient::findOrFail($patientId);

        if ($patient->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized access to this patient.');
        }

        $validated = $request->validate([
            'plan_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'recovery_probability' => 'nullable|numeric|min:0|max:1',
            'ml_confidence_score' => 'nullable|numeric|min:0|max:1',
            'difficulty_level' => 'required|in:1,2,3,4,5',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $plan = RehabPlan::create([
            'patient_id' => $patient->id,
            'clinician_id' => $clinician->id,
            'plan_name' => $validated['plan_name'],
            'description' => $validated['description'],
            'recovery_probability' => $validated['recovery_probability'],
            'ml_confidence_score' => $validated['ml_confidence_score'],
            'difficulty_level' => $validated['difficulty_level'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'draft',
        ]);

        // Automatically generate exercises based on ML recommendations
        $this->generateExercisesForPlan($plan, $patient);

        return redirect()->route('clinician.plans.edit', $plan->id)
            ->with('success', 'Rehabilitation plan created with recommended exercises.');
    }

    /**
     * Generate exercises for a plan based on ML recommendations and patient deficits
     */
    private function generateExercisesForPlan(RehabPlan $plan, Patient $patient)
    {
        try {
            $mlService = new MLPredictionService();

            // Prepare clinical data for ML service
            // Gender: M=1, F=0
            $genderValue = ($patient->gender === 'F') ? 0 : 1;

            $clinicalData = [
                'age' => (int) ($patient->age ?? 0),
                'gender' => $genderValue,
                'rsbp' => (int) ($patient->rsbp ?? 0),
                'stroke_subtype' => $patient->stroke_subtype ?? 'OTH',
                'conscious_state' => $patient->conscious_state ?? 'Alert',
                'rdef1' => (bool) $patient->rdef1,
                'rdef2' => (bool) $patient->rdef2,
                'rdef3' => (bool) $patient->rdef3,
                'rdef4' => (bool) $patient->rdef4,
                'rdef5' => (bool) $patient->rdef5,
                'rdef6' => (bool) $patient->rdef6,
                'rdef7' => (bool) $patient->rdef7,
                'rdef8' => (bool) $patient->rdef8,
            ];

            // Get ML recommendations
            $mlPrediction = $mlService->predictRecoveryWithISTData($clinicalData);

            \Log::info('ML Prediction Response:', $mlPrediction);

            if (isset($mlPrediction['recommended_exercises']) && !empty($mlPrediction['recommended_exercises'])) {
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                $dayIndex = 0;

                // Add recommended exercises to the plan
                foreach ($mlPrediction['recommended_exercises'] as $recommendation) {
                    \Log::info('Processing exercise recommendation:', $recommendation);

                    // Find exercise by name or create a reference
                    $exercise = Exercise::where('name', $recommendation['name'])
                        ->orWhere('name', 'like', '%' . $recommendation['name'] . '%')
                        ->first();

                    if ($exercise) {
                        $day = $days[$dayIndex % 7];
                        $dayIndex++;

                        // Extract numeric reps from progression_reps string (e.g., "3 sets of 10 reps" -> 10)
                        $customReps = null;
                        if (isset($recommendation['progression_reps'])) {
                            preg_match('/\d+(?=\s*reps)/', $recommendation['progression_reps'], $matches);
                            if (!empty($matches)) {
                                $customReps = (int) $matches[0];
                            }
                        }

                        PlanExercise::create([
                            'rehab_plan_id' => $plan->id,
                            'exercise_id' => $exercise->id,
                            'day_of_week' => $day,
                            'frequency_per_week' => 3,
                            'scheduled_time' => '09:00',
                            'custom_repetitions' => $customReps,
                            'custom_duration_minutes' => 30,
                            'notes' => $recommendation['safety_notes'] ?? null,
                        ]);

                        \Log::info("Added exercise: {$exercise->name} to plan {$plan->id}");
                    } else {
                        \Log::warning("Exercise not found in database: {$recommendation['name']}");
                    }
                }
            } else {
                \Log::warning('No recommended exercises in ML response');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to generate exercises automatically: ' . $e->getMessage());
            // Continue without auto-generation - user can add manually
        }
    }

    public function edit($planId)
    {
        $clinician = auth()->user();
        $plan = RehabPlan::findOrFail($planId);

        if ($plan->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized access to this plan.');
        }

        $exercises = Exercise::where('difficulty_level', '<=', $plan->difficulty_level)->get();
        $planExercises = $plan->exercises()->with('exercise')->get();

        // Group exercises by exercise_id to consolidate multiple days into one entry
        $groupedExercises = $planExercises->groupBy('exercise_id')->map(function ($group) {
            return [
                'exercise_id' => $group->first()->exercise_id,
                'exercise' => $group->first()->exercise,
                'frequency_per_week' => $group->first()->frequency_per_week,
                'scheduled_time' => $group->first()->scheduled_time,
                'custom_repetitions' => $group->first()->custom_repetitions,
                'custom_duration_minutes' => $group->first()->custom_duration_minutes,
                'days' => $group->pluck('day_of_week')->toArray(),
                'plan_exercises' => $group->toArray(),
            ];
        });

        return view('clinician.plans.edit', [
            'plan' => $plan,
            'exercises' => $exercises,
            'planExercises' => $planExercises,
            'groupedExercises' => $groupedExercises,
        ]);
    }

    public function addExercise(Request $request, $planId)
    {
        $clinician = auth()->user();
        $plan = RehabPlan::findOrFail($planId);

        if ($plan->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized access to this plan.');
        }

        $validated = $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'frequency_per_week' => 'required|integer|min:1|max:7',
            'scheduled_time' => 'nullable|date_format:H:i',
            'custom_repetitions' => 'nullable|integer|min:1',
            'custom_duration_minutes' => 'nullable|integer|min:1',
        ]);

        // Create a PlanExercise record for each selected day
        foreach ($validated['days_of_week'] as $day) {
            PlanExercise::create([
                'rehab_plan_id' => $plan->id,
                'exercise_id' => $validated['exercise_id'],
                'day_of_week' => $day,
                'frequency_per_week' => $validated['frequency_per_week'],
                'scheduled_time' => $validated['scheduled_time'],
                'custom_repetitions' => $validated['custom_repetitions'],
                'custom_duration_minutes' => $validated['custom_duration_minutes'],
            ]);
        }

        return back()->with('success', 'Exercise added to plan for ' . count($validated['days_of_week']) . ' day(s).');
    }

    public function updateExercise(Request $request, $planId, $planExerciseId)
    {
        \Log::info('=== UPDATE EXERCISE METHOD CALLED ===');
        \Log::info('planId: ' . $planId . ', planExerciseId: ' . $planExerciseId);

        $clinician = auth()->user();
        $plan = RehabPlan::findOrFail($planId);

        if ($plan->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized access to this plan.');
        }

        $planExercise = PlanExercise::findOrFail($planExerciseId);
        $exerciseId = $planExercise->exercise_id;

        \Log::info('Update Exercise Request:', [
            'planExerciseId' => $planExerciseId,
            'request_method' => $request->method(),
            'request_data' => $request->all(),
        ]);

        $validated = $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'frequency_per_week' => 'required|integer|min:1|max:7',
            'scheduled_time' => 'nullable|date_format:H:i:s',
            'custom_repetitions' => 'nullable|integer|min:1',
            'custom_duration_minutes' => 'nullable|integer|min:1',
        ]);

        \Log::info('Validated data:', $validated);

        // Delete all existing PlanExercise records for this exercise in this plan
        PlanExercise::where('rehab_plan_id', $plan->id)
            ->where('exercise_id', $exerciseId)
            ->delete();

        // Create new records for all selected days
        foreach ($validated['days_of_week'] as $day) {
            PlanExercise::create([
                'rehab_plan_id' => $plan->id,
                'exercise_id' => $validated['exercise_id'],
                'day_of_week' => $day,
                'frequency_per_week' => $validated['frequency_per_week'],
                'scheduled_time' => $validated['scheduled_time'],
                'custom_repetitions' => $validated['custom_repetitions'],
                'custom_duration_minutes' => $validated['custom_duration_minutes'],
            ]);
        }

        \Log::info('Exercise updated for all days');

        return back()->with('success', 'Exercise updated successfully for ' . count($validated['days_of_week']) . ' day(s).');
    }

    public function removeExercise($planExerciseId)
    {
        $clinician = auth()->user();
        $planExercise = PlanExercise::findOrFail($planExerciseId);
        $plan = $planExercise->rehabPlan;

        if ($plan->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized access to this plan.');
        }

        $planExercise->delete();

        return back()->with('success', 'Exercise removed from plan.');
    }

    public function deletePlan($planId)
    {
        $clinician = auth()->user();
        $plan = RehabPlan::findOrFail($planId);

        if ($plan->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized access to this plan.');
        }

        $planName = $plan->plan_name;

        $plan->delete();

        return redirect()->route('clinician.plans.index')
            ->with('success', 'Rehabilitation plan "' . $planName . '" has been deleted successfully.');
    }

    public function publish($planId)
    {
        $clinician = auth()->user();
        $plan = RehabPlan::findOrFail($planId);

        if ($plan->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized access to this plan.');
        }

        $plan->update(['status' => 'active']);

        return redirect()->route('clinician.patients.show', $plan->patient_id)
            ->with('success', 'Rehabilitation plan published and activated.');
    }
}
