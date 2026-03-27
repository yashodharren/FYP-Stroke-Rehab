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

        if ($mlAvailable) {
            try {
                $mlPrediction = $mlService->predictRecovery(
                    $patient->age,
                    $patient->stroke_type,
                    $patient->deficit_area,
                    $patient->medical_history
                );
            } catch (\Exception $e) {
                \Log::warning('ML prediction failed: ' . $e->getMessage());
            }
        }

        return view('clinician.plans.create', [
            'patient' => $patient,
            'mlPrediction' => $mlPrediction,
            'mlAvailable' => $mlAvailable,
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
            'difficulty_level' => $validated['difficulty_level'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'draft',
        ]);

        return redirect()->route('clinician.plans.edit', $plan->id)
            ->with('success', 'Rehabilitation plan created. Now add exercises.');
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

        return view('clinician.plans.edit', [
            'plan' => $plan,
            'exercises' => $exercises,
            'planExercises' => $planExercises,
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
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'frequency_per_week' => 'required|integer|min:1|max:7',
            'scheduled_time' => 'nullable|date_format:H:i',
            'custom_repetitions' => 'nullable|integer|min:1',
            'custom_duration_minutes' => 'nullable|integer|min:1',
        ]);

        PlanExercise::create([
            'rehab_plan_id' => $plan->id,
            'exercise_id' => $validated['exercise_id'],
            'day_of_week' => $validated['day_of_week'],
            'frequency_per_week' => $validated['frequency_per_week'],
            'scheduled_time' => $validated['scheduled_time'],
            'custom_repetitions' => $validated['custom_repetitions'],
            'custom_duration_minutes' => $validated['custom_duration_minutes'],
        ]);

        return back()->with('success', 'Exercise added to plan.');
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
