<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientFeedback;
use App\Models\PlanExercise;
use App\Models\RehabPlan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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

        // Check if feedback should be prompted
        $showFeedbackPrompt = $activePlan ? $this->shouldPromptFeedback($activePlan, $planExercises) : false;

        return view('patient.dashboard', [
            'patient' => $patient,
            'activePlan' => $activePlan,
            'planExercises' => $planExercises,
            'upcomingExercises' => $upcomingExercises,
            'showFeedbackPrompt' => $showFeedbackPrompt,
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

    /**
     * Toggle exercise completion (mark done / unmark done).
     */
    public function markDone(Request $request, $planExerciseId)
    {
        $user = auth()->user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $planExercise = PlanExercise::with('rehabPlan')->findOrFail($planExerciseId);

        if (!$planExercise->rehabPlan || $planExercise->rehabPlan->patient_id !== $patient->id) {
            abort(403, 'Unauthorized access to this exercise.');
        }

        $isNowCompleted = !$planExercise->is_completed;

        $planExercise->update([
            'is_completed' => $isNowCompleted,
            'completed_at' => $isNowCompleted ? now() : null,
        ]);

        $message = $isNowCompleted ? 'Exercise marked as done!' : 'Exercise marked as not done.';
        return back()->with('success', $message);
    }

    /**
     * Submit overall plan feedback (triggered after 1 month + 60% completion).
     */
    public function submitPlanFeedback(Request $request, $planId)
    {
        $user = auth()->user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $plan = RehabPlan::where('id', $planId)
            ->where('patient_id', $patient->id)
            ->firstOrFail();

        $validated = $request->validate([
            'exercises' => 'required|array',
            'exercises.*.plan_exercise_id' => 'required|exists:plan_exercises,id',
            'exercises.*.pain_level' => 'nullable|integer|min:0|max:10',
            'exercises.*.difficulty_rating' => 'nullable|integer|min:1|max:5',
            'overall_comments' => 'nullable|string|max:1000',
        ]);

        foreach ($validated['exercises'] as $exerciseFeedback) {
            PatientFeedback::create([
                'patient_id' => $patient->id,
                'plan_exercise_id' => $exerciseFeedback['plan_exercise_id'],
                'rehab_plan_id' => $plan->id,
                'is_plan_feedback' => true,
                'pain_level' => $exerciseFeedback['pain_level'] ?? null,
                'difficulty_rating' => $exerciseFeedback['difficulty_rating'] ?? null,
                'overall_comments' => $validated['overall_comments'] ?? null,
                'completed_exercise' => true,
                'feedback_date' => now(),
            ]);
        }

        $plan->update([
            'feedback_requested' => false,
            'feedback_requested_at' => null,
        ]);

        return redirect()->route('patient.dashboard')
            ->with('success', 'Thank you for your feedback! Your clinician will review and may recommend a new plan.');
    }

    /**
     * Check if the feedback prompt should be shown to the patient.
     * Triggers when plan is >= 1 month old AND >= 60% exercises are completed.
     */
    private function shouldPromptFeedback($activePlan, $planExercises): bool
    {
        if (!$activePlan || $activePlan->feedback_requested) {
            return false;
        }

        // Check 1 month elapsed since plan start
        if (!$activePlan->start_date || $activePlan->start_date->diffInDays(now()) < 30) {
            return false;
        }

        // Check completion rate >= 60%
        $total = $planExercises->count();
        if ($total === 0) {
            return false;
        }

        $completed = $planExercises->where('is_completed', true)->count();
        $completionRate = $completed / $total;

        if ($completionRate < 0.6) {
            return false;
        }

        // Mark that we've requested feedback so the prompt only shows once
        $activePlan->update([
            'feedback_requested' => true,
            'feedback_requested_at' => now(),
        ]);

        return true;
    }

    /**
     * Allow the patient to reschedule one of their own plan exercises.
     */
    public function rescheduleExercise(Request $request, $planExerciseId)
    {
        $user = auth()->user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        $planExercise = PlanExercise::with('rehabPlan')->findOrFail($planExerciseId);

        // Ensure this exercise belongs to a plan for the logged-in patient
        if (!$planExercise->rehabPlan || $planExercise->rehabPlan->patient_id !== $patient->id) {
            abort(403, 'Unauthorized access to this exercise.');
        }

        $validated = $request->validate([
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'scheduled_time' => 'required|date_format:H:i',
        ]);

        $planExercise->update([
            'day_of_week' => $validated['day_of_week'],
            'scheduled_time' => $validated['scheduled_time'],
        ]);

        return back()->with('success', "Exercise rescheduled to {$validated['day_of_week']} at {$validated['scheduled_time']}.");
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

    public function showProfile()
    {
        return view('patient.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('patient.profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('patient.profile.show')
            ->with('success', 'Password changed successfully.');
    }
}
