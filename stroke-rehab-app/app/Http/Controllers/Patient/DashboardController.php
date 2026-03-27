<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientFeedback;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();
        $activePlan = $patient->rehabPlans()->where('status', 'active')->first();
        $planExercises = $activePlan ? $activePlan->exercises()->with('exercise')->get() : collect();

        return view('patient.dashboard', [
            'patient' => $patient,
            'activePlan' => $activePlan,
            'planExercises' => $planExercises,
        ]);
    }

    public function schedule()
    {
        $user = auth()->user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();
        $activePlan = $patient->rehabPlans()->where('status', 'active')->first();
        $planExercises = $activePlan ? $activePlan->exercises()->with('exercise')->get() : collect();

        $schedule = $this->buildWeeklySchedule($planExercises);

        return view('patient.schedule', [
            'patient' => $patient,
            'activePlan' => $activePlan,
            'schedule' => $schedule,
        ]);
    }

    public function submitFeedback(Request $request, $planExerciseId)
    {
        $user = auth()->user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'pain_level' => 'nullable|integer|min:0|max:10',
            'difficulty_rating' => 'nullable|integer|min:1|max:5',
            'mood_rating' => 'nullable|integer|min:1|max:5',
            'comments' => 'nullable|string|max:500',
            'completed_exercise' => 'required|boolean',
        ]);

        PatientFeedback::create([
            'patient_id' => $patient->id,
            'plan_exercise_id' => $planExerciseId,
            'pain_level' => $validated['pain_level'],
            'difficulty_rating' => $validated['difficulty_rating'],
            'mood_rating' => $validated['mood_rating'],
            'comments' => $validated['comments'],
            'completed_exercise' => $validated['completed_exercise'],
            'feedback_date' => now(),
        ]);

        return back()->with('success', 'Feedback submitted successfully.');
    }

    private function buildWeeklySchedule($planExercises)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $schedule = [];

        foreach ($days as $day) {
            $schedule[$day] = $planExercises->filter(function ($pe) use ($day) {
                return $pe->day_of_week === $day;
            })->values();
        }

        return $schedule;
    }
}
