<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientFeedback;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();
        $activePlan = $patient->rehabPlans()->where('status', 'active')->first();
        $planExercises = $activePlan ? $activePlan->exercises()->with('exercise')->get() : collect();

        // Filter exercises for next 24 hours
        $upcomingExercises = $this->getNext24HoursExercises($planExercises);

        return view('patient.dashboard', [
            'patient' => $patient,
            'activePlan' => $activePlan,
            'planExercises' => $planExercises,
            'upcomingExercises' => $upcomingExercises,
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

    public function details()
    {
        $user = auth()->user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        return view('patient.details', [
            'patient' => $patient,
            'user' => $user,
        ]);
    }

    public function appointments()
    {
        $user = auth()->user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        return view('patient.appointments', [
            'patient' => $patient,
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

    private function getNext24HoursExercises($planExercises)
    {
        $now = Carbon::now();
        $next24Hours = $now->copy()->addHours(24);
        $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $upcomingExercises = collect();

        // Iterate through the next 24 hours
        for ($i = 0; $i < 24; $i++) {
            $checkTime = $now->copy()->addHours($i);
            $dayOfWeek = $dayNames[$checkTime->dayOfWeek];
            $currentHour = $checkTime->format('H');

            // Find exercises for this day and hour
            $exercisesForTime = $planExercises->filter(function ($pe) use ($dayOfWeek, $currentHour) {
                if ($pe->day_of_week !== $dayOfWeek) {
                    return false;
                }

                // Extract hour from scheduled_time (HH:mm:ss or HH:mm format)
                $exerciseHour = substr($pe->scheduled_time, 0, 2);
                return $exerciseHour == $currentHour;
            });

            $upcomingExercises = $upcomingExercises->merge($exercisesForTime);
        }

        return $upcomingExercises->unique('id')->sortBy(function ($pe) {
            return $pe->scheduled_time ?? '23:59';
        })->values();
    }

    private function buildWeeklySchedule($planExercises)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $schedule = [];
        $timeSlots = [];

        // Initialize schedule with empty arrays for each day
        foreach ($days as $day) {
            $schedule[$day] = $planExercises->filter(function ($pe) use ($day) {
                return $pe->day_of_week === $day;
            })->sortBy(function ($pe) {
                // Sort by scheduled_time if available
                return $pe->scheduled_time ?? '23:59';
            })->values();
        }

        // Build time-based schedule (9 AM to 5 PM)
        for ($hour = 9; $hour <= 17; $hour++) {
            $timeSlot = sprintf('%02d:00', $hour);
            $timeSlots[$timeSlot] = [];

            foreach ($days as $day) {
                $exercise = $planExercises->filter(function ($pe) use ($day, $timeSlot) {
                    // Extract HH:mm from scheduled_time (handles both HH:mm and HH:mm:ss formats)
                    $exerciseTime = substr($pe->scheduled_time, 0, 5);
                    return $pe->day_of_week === $day && $exerciseTime === $timeSlot;
                })->first();

                $timeSlots[$timeSlot][$day] = $exercise;
            }
        }

        return [
            'byDay' => $schedule,
            'byTime' => $timeSlots,
        ];
    }
}
