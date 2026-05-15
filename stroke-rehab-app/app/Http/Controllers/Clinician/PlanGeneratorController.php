<?php

namespace App\Http\Controllers\Clinician;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientFeedback;
use App\Models\RehabPlan;
use App\Models\Exercise;
use App\Models\PlanExercise;
use App\Services\MLPredictionService;
use Illuminate\Http\Request;

class PlanGeneratorController extends Controller
{
    public function create(Request $request, $patientId)
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

        // Check if this plan creation is based on patient feedback
        $fromFeedbackPlanId = $request->query('from_feedback');
        $feedbackSuggestion = null;

        if ($fromFeedbackPlanId) {
            // Find the most recent submission session (max feedback_date rounded to minute)
            $latestSession = PatientFeedback::where('patient_id', $patient->id)
                ->where('rehab_plan_id', $fromFeedbackPlanId)
                ->where('is_plan_feedback', true)
                ->max('feedback_date');

            $feedbackItems = collect();
            if ($latestSession) {
                $sessionMinute = \Carbon\Carbon::parse($latestSession)->format('Y-m-d H:i');
                $feedbackItems = PatientFeedback::where('patient_id', $patient->id)
                    ->where('rehab_plan_id', $fromFeedbackPlanId)
                    ->where('is_plan_feedback', true)
                    ->get()
                    ->filter(fn($fb) => $fb->feedback_date &&
                        \Carbon\Carbon::parse($fb->feedback_date)->format('Y-m-d H:i') === $sessionMinute);
            }

            if ($feedbackItems->isNotEmpty()) {
                $avgDifficulty = $feedbackItems->avg('difficulty_rating');
                $avgPain = $feedbackItems->avg('pain_level');

                // Get the previous plan's difficulty level
                $prevPlan = RehabPlan::find($fromFeedbackPlanId);
                $prevDifficulty = $prevPlan ? $prevPlan->difficulty_level : 3;

                // Adjust difficulty based on feedback averages
                if ($avgDifficulty >= 4 || $avgPain >= 7) {
                    $suggestedDifficulty = max(1, $prevDifficulty - 1);
                    $reason = 'exercises were too difficult or painful';
                } elseif ($avgDifficulty <= 2 && $avgPain <= 2) {
                    $suggestedDifficulty = min(5, $prevDifficulty + 1);
                    $reason = 'patient found exercises easy and had low pain';
                } else {
                    $suggestedDifficulty = $prevDifficulty;
                    $reason = 'previous difficulty level was appropriate';
                }

                $feedbackSuggestion = [
                    'suggested_difficulty' => $suggestedDifficulty,
                    'avg_pain' => round($avgPain, 1),
                    'avg_difficulty' => round($avgDifficulty, 1),
                    'reason' => $reason,
                    'overall_comments' => $feedbackItems->first()->overall_comments,
                ];
            }
        }

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

        // If feedback suggests a difficulty, override the ML difficulty level so the
        // display and exercise generation both use the feedback-adjusted level.
        if (!empty($feedbackSuggestion) && $mlPrediction) {
            $targetDifficulty = (int) $feedbackSuggestion['suggested_difficulty'];
            $mlPrediction['difficulty_level'] = $targetDifficulty;

            // Build a progressive mixed-difficulty exercise list:
            // ~1 exercise at (target-2), ~2 at (target-1), ~3 at target (clamped ≥ 1).
            $mixedExercises = collect();

            $tiers = [
                max(1, $targetDifficulty - 2) => 1,
                max(1, $targetDifficulty - 1) => 2,
                $targetDifficulty             => 3,
            ];

            // Collapse duplicate tiers (e.g. when target=1, all map to 1)
            $tierCounts = [];
            foreach ($tiers as $level => $count) {
                $tierCounts[$level] = ($tierCounts[$level] ?? 0) + $count;
            }

            $targetAreas = $this->getPatientTargetAreas($patient);

            foreach ($tierCounts as $level => $count) {
                $query = \App\Models\Exercise::where('difficulty_level', $level);
                if (!empty($targetAreas)) {
                    $query->whereIn('target_area', $targetAreas);
                }
                $exercises = $query->inRandomOrder()->limit($count)->get();

                foreach ($exercises as $ex) {
                    $mixedExercises->push([
                        'name'               => $ex->name,
                        'target_deficit'     => ucfirst(str_replace('_', ' ', $ex->target_area)),
                        'body_region'        => ucfirst(str_replace('_', ' ', $ex->target_area)),
                        'difficulty'         => $ex->difficulty_level,
                        'instructions'       => $ex->instructions,
                        'frequency_per_week' => 3,
                        'progression_reps'   => $ex->repetitions ?? null,
                        'safety_notes'       => null,
                    ]);
                }
            }

            // Sort ascending by difficulty so the list reads easy → hard
            $mlPrediction['recommended_exercises'] = $mixedExercises
                ->sortBy('difficulty')
                ->values()
                ->all();
        }

        return view('clinician.plans.create', [
            'patient' => $patient,
            'mlPrediction' => $mlPrediction,
            'mlAvailable' => $mlAvailable,
            'mlError' => $mlError,
            'feedbackSuggestion' => $feedbackSuggestion,
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
        $exercisesGenerated = $this->generateExercisesForPlan($plan, $patient);

        $message = $exercisesGenerated
            ? 'Rehabilitation plan created with recommended exercises.'
            : 'Rehabilitation plan created. Please add exercises manually.';

        return redirect()->route('clinician.plans.edit', $plan->id)
            ->with('success', $message);
    }

    /**
     * Generate exercises for a plan based on ML recommendations and patient deficits
     * 
     * @return bool True if exercises were successfully generated, false otherwise
     */
    private function generateExercisesForPlan(RehabPlan $plan, Patient $patient): bool
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

            // Override ML difficulty with the plan's saved difficulty_level (which already
            // incorporates any feedback-based adjustment chosen by the clinician)
            $planDifficulty = (int) $plan->difficulty_level;
            $targetAreas    = $this->getPatientTargetAreas($patient);

            // Filter recommended exercises to only those at or below the plan's difficulty
            // and belonging to the patient's deficit areas
            if (isset($mlPrediction['recommended_exercises'])) {
                $mlPrediction['recommended_exercises'] = array_filter(
                    $mlPrediction['recommended_exercises'],
                    fn($ex) => isset($ex['difficulty']) ? (int)$ex['difficulty'] <= $planDifficulty : true
                );
            }

            if (isset($mlPrediction['recommended_exercises']) && !empty($mlPrediction['recommended_exercises'])) {
                // Define day patterns to avoid collisions between exercises
                // Pattern 1: Monday, Wednesday, Friday (for odd-indexed exercises: 0, 2, 4...)
                // Pattern 2: Tuesday, Thursday, Saturday (for even-indexed exercises: 1, 3, 5...)
                $dayPatterns = [
                    [
                        1 => 'Monday',
                        2 => ['Monday', 'Wednesday'],
                        3 => ['Monday', 'Wednesday', 'Friday'],
                    ],
                    [
                        1 => 'Tuesday',
                        2 => ['Tuesday', 'Thursday'],
                        3 => ['Tuesday', 'Thursday', 'Saturday'],
                    ],
                ];

                $exercisesAdded = 0;
                $exerciseIndex = 0;

                // Add recommended exercises to the plan
                foreach ($mlPrediction['recommended_exercises'] as $recommendation) {
                    // Skip exercises outside the patient's deficit target areas
                    if (!empty($targetAreas)) {
                        $exArea = strtolower(str_replace(' ', '_', $recommendation['body_region'] ?? ''));
                        if (!in_array($exArea, $targetAreas, true)) {
                            continue;
                        }
                    }
                    \Log::info('Processing exercise recommendation:', $recommendation);

                    // Find exercise by name (case-insensitive exact match first, then fuzzy match)
                    $recommendedName = $recommendation['name'];
                    $exercise = Exercise::whereRaw('LOWER(name) = ?', [strtolower($recommendedName)])
                        ->first();

                    // If exact match not found, try fuzzy match
                    if (!$exercise) {
                        $exercise = Exercise::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($recommendedName) . '%'])
                            ->first();
                    }

                    if ($exercise) {
                        // Get frequency from recommendation or default to 3
                        $frequency = isset($recommendation['frequency_per_week'])
                            ? (int) $recommendation['frequency_per_week']
                            : 3;

                        // Extract numeric reps from progression_reps string (e.g., "3 sets of 10 reps" -> 10)
                        $customReps = null;
                        if (isset($recommendation['progression_reps'])) {
                            preg_match('/\d+(?=\s*reps)/', $recommendation['progression_reps'], $matches);
                            if (!empty($matches)) {
                                $customReps = (int) $matches[0];
                            }
                        }

                        // Select day pattern based on exercise index (alternate between pattern 0 and 1)
                        $patternIndex = $exerciseIndex % 2;
                        $selectedDays = $dayPatterns[$patternIndex][$frequency];

                        // Ensure selectedDays is always an array
                        if (!is_array($selectedDays)) {
                            $selectedDays = [$selectedDays];
                        }

                        // Calculate time slot for this exercise (stagger by exercise index)
                        $baseHour = 9 + $exerciseIndex; // 9:00, 10:00, 11:00, etc.
                        if ($baseHour > 17) {
                            $baseHour = 17; // Cap at 5 PM
                        }
                        $scheduledTime = sprintf('%02d:00', $baseHour);

                        // Create a PlanExercise record for each day
                        foreach ($selectedDays as $day) {
                            PlanExercise::create([
                                'rehab_plan_id' => $plan->id,
                                'exercise_id' => $exercise->id,
                                'day_of_week' => $day,
                                'frequency_per_week' => $frequency,
                                'scheduled_time' => $scheduledTime,
                                'custom_repetitions' => $customReps,
                                'custom_duration_minutes' => 30,
                                'notes' => $recommendation['safety_notes'] ?? null,
                            ]);
                        }

                        $exercisesAdded++;
                        $exerciseIndex++;
                        \Log::info("Added exercise: {$exercise->name} to plan {$plan->id} for {$frequency} day(s) using pattern {$patternIndex}");
                    } else {
                        \Log::warning("Exercise not found in database: {$recommendation['name']}");
                    }
                }

                return $exercisesAdded > 0;
            } else {
                \Log::warning('No recommended exercises in ML response');
                return false;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to generate exercises automatically: ' . $e->getMessage());
            return false;
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
            'scheduled_times' => 'nullable|array',
            'custom_repetitions' => 'nullable|integer|min:1',
            'custom_duration_minutes' => 'nullable|integer|min:1',
        ]);

        // Get the next available time slot to avoid conflicts
        $nextTimeSlot = $this->getNextAvailableTimeSlot($plan->id);

        // Build scheduled_times array for per-day times
        $scheduledTimes = [];
        if ($request->has('scheduled_times') && is_array($validated['scheduled_times'])) {
            // Use custom per-day times if provided
            foreach ($validated['days_of_week'] as $day) {
                $scheduledTimes[$day] = $validated['scheduled_times'][$day] ?? $nextTimeSlot;
            }
        } else {
            // Use the same time for all days if no per-day times provided
            $timeToUse = $validated['scheduled_time'] ?? $nextTimeSlot;
            foreach ($validated['days_of_week'] as $day) {
                $scheduledTimes[$day] = $timeToUse;
            }
        }

        // Create a PlanExercise record for each selected day
        foreach ($validated['days_of_week'] as $day) {
            PlanExercise::create([
                'rehab_plan_id' => $plan->id,
                'exercise_id' => $validated['exercise_id'],
                'day_of_week' => $day,
                'frequency_per_week' => $validated['frequency_per_week'],
                'scheduled_time' => $scheduledTimes[$day],
                'scheduled_times' => $scheduledTimes,
                'custom_repetitions' => $validated['custom_repetitions'],
                'custom_duration_minutes' => $validated['custom_duration_minutes'],
            ]);
        }

        return back()->with('success', 'Exercise added to plan for ' . count($validated['days_of_week']) . ' day(s).');
    }

    /**
     * Derive DB target_area values from the patient's IST deficit flags.
     * Returns an array of target_area strings to use in whereIn() queries.
     * Falls back to all areas if no deficits are set.
     */
    private function getPatientTargetAreas(Patient $patient): array
    {
        $areas = [];

        if ($patient->rdef1) $areas[] = 'face';          // Face Deficit
        if ($patient->rdef2) $areas[] = 'upper_limb';    // Arm/Hand Deficit
        if ($patient->rdef3) $areas[] = 'lower_limb';    // Leg/Foot Deficit
        if ($patient->rdef4) $areas[] = 'face';          // Dysphasia (Speech)
        if ($patient->rdef5) $areas[] = 'face';          // Hemianopia (Vision)
        if ($patient->rdef6) {
            $areas[] = 'lower_limb';
            $areas[] = 'general';
        } // Visuospatial
        if ($patient->rdef7) {
            $areas[] = 'lower_limb';
            $areas[] = 'general';
        } // Brainstem/Cerebellar
        if ($patient->rdef8) $areas[] = 'general';       // Other Deficits

        return array_unique($areas);
    }

    /**
     * Get the next available time slot to avoid exercise conflicts
     * Returns time in H:i format, incrementing by 1 hour from base time
     */
    private function getNextAvailableTimeSlot($planId): string
    {
        $baseHour = 9; // Start at 9:00 AM
        $maxHour = 17; // End at 5:00 PM

        // Get all scheduled times for this plan
        $planExercises = PlanExercise::where('rehab_plan_id', $planId)->get();

        if ($planExercises->isEmpty()) {
            return sprintf('%02d:00', $baseHour);
        }

        // Extract all used hours
        $usedHours = [];
        foreach ($planExercises as $exercise) {
            if ($exercise->scheduled_time) {
                $hour = (int) explode(':', $exercise->scheduled_time)[0];
                $usedHours[$hour] = true;
            }
        }

        // Find next available hour
        for ($hour = $baseHour; $hour <= $maxHour; $hour++) {
            if (!isset($usedHours[$hour])) {
                return sprintf('%02d:00', $hour);
            }
        }

        // If all hours are taken, wrap around or use the last available
        return sprintf('%02d:00', $maxHour);
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
            'scheduled_time' => 'nullable|date_format:H:i',
            'scheduled_times' => 'nullable|array',
            'custom_repetitions' => 'nullable|integer|min:1',
            'custom_duration_minutes' => 'nullable|integer|min:1',
        ]);

        \Log::info('Validated data:', $validated);

        // Build scheduled_times array for per-day times
        $scheduledTimes = [];
        if ($request->has('scheduled_times') && is_array($validated['scheduled_times'])) {
            // Use custom per-day times if provided
            foreach ($validated['days_of_week'] as $day) {
                $scheduledTimes[$day] = $validated['scheduled_times'][$day] ?? $validated['scheduled_time'];
            }
        } else {
            // Use the same time for all days if no per-day times provided
            foreach ($validated['days_of_week'] as $day) {
                $scheduledTimes[$day] = $validated['scheduled_time'];
            }
        }

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
                'scheduled_time' => $scheduledTimes[$day],
                'scheduled_times' => $scheduledTimes,
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
        $patientId = $plan->patient_id;

        $plan->delete();

        return redirect()->route('clinician.patients.show', $patientId)
            ->with('success', 'Rehabilitation plan "' . $planName . '" has been deleted successfully.');
    }

    /**
     * Update the status of a rehab plan.
     * Enforces "only one active plan per patient" when activating.
     */
    public function updateStatus(Request $request, $planId)
    {
        $clinician = auth()->user();
        $plan = RehabPlan::findOrFail($planId);

        if ($plan->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized access to this plan.');
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,active,completed,paused',
        ]);

        $pausedCount = 0;

        // If activating, pause any other active plan for the same patient
        if ($validated['status'] === 'active') {
            $pausedCount = RehabPlan::where('patient_id', $plan->patient_id)
                ->where('id', '!=', $plan->id)
                ->where('status', 'active')
                ->update(['status' => 'paused']);
        }

        $plan->update(['status' => $validated['status']]);

        $message = "Plan status updated to '{$validated['status']}'.";
        if ($pausedCount > 0) {
            $message .= " {$pausedCount} previous active plan(s) have been paused.";
        }

        return back()->with('success', $message);
    }

    public function publish($planId)
    {
        $clinician = auth()->user();
        $plan = RehabPlan::findOrFail($planId);

        if ($plan->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized access to this plan.');
        }

        // Enforce only one active plan per patient: pause any other currently
        // active plan(s) for this patient before activating the new one.
        $pausedCount = RehabPlan::where('patient_id', $plan->patient_id)
            ->where('id', '!=', $plan->id)
            ->where('status', 'active')
            ->update(['status' => 'paused']);

        $plan->update(['status' => 'active']);

        $message = $pausedCount > 0
            ? "Rehabilitation plan published and activated. {$pausedCount} previous active plan(s) have been paused."
            : 'Rehabilitation plan published and activated.';

        return redirect()->route('clinician.patients.show', $plan->patient_id)
            ->with('success', $message);
    }
}
