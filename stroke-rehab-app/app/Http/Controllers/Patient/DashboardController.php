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

        // Filter exercises for today only
        $upcomingExercises = $this->getTodaysExercises($planExercises);

        // Check if feedback should be prompted
        $showFeedbackPrompt = $activePlan ? $this->shouldPromptFeedback($activePlan, $planExercises) : false;

        // Only mark feedback_requested AFTER we know the view will render successfully
        if ($showFeedbackPrompt) {
            $activePlan->update([
                'feedback_requested'    => true,
                'feedback_requested_at' => now(),
            ]);
        }

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

    public function progress()
    {
        $user = auth()->user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();
        $activePlan = $patient->rehabPlans()->where('status', 'active')->first();
        $planExercises = $activePlan ? $activePlan->exercises()->with('exercise')->get() : collect();

        $days      = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $dayOrder  = array_flip($days);
        $today     = Carbon::now()->startOfDay();
        $todayName = Carbon::now()->format('l');
        $planStart = $activePlan && $activePlan->start_date
            ? Carbon::parse($activePlan->start_date)->startOfDay()
            : $today;

        // ── Cumulative totals from plan start to today ──────────────────────
        // For each exercise slot (day_of_week), count how many times that day
        // has occurred since the plan started up to and including today.
        $cumulativeTotal     = 0;
        $cumulativeCompleted = 0; // = exercises with completed_at set (all-time)
        $cumulativeMissed    = 0;

        // How many times each weekday has occurred since plan start through today
        $dayOccurrences = [];
        foreach ($days as $day) {
            $dayIndex  = $dayOrder[$day]; // 0=Mon … 6=Sun
            $first     = $planStart->copy()->next($day);
            // If plan started on or before that weekday this week, start from plan-start week
            $firstOccurrence = $planStart->copy()->startOfWeek(Carbon::MONDAY)->addDays($dayIndex);
            if ($firstOccurrence->lt($planStart)) {
                $firstOccurrence->addWeek();
            }
            $count = 0;
            if ($firstOccurrence->lte($today)) {
                $diffDays = $firstOccurrence->diffInDays($today);
                $count    = (int) floor($diffDays / 7) + 1;
            }
            $dayOccurrences[$day] = $count;
        }

        // Cumulative completed = exercises that have a completed_at timestamp
        $completedExercises = $planExercises->filter(fn($pe) => $pe->completed_at)->count();

        // Cumulative total = sum of occurrences × exercises scheduled on that day
        foreach ($days as $day) {
            $slotCount          = $planExercises->where('day_of_week', $day)->count();
            $cumulativeTotal   += $dayOccurrences[$day] * $slotCount;
        }

        // Cumulative missed = past scheduled occurrences not accounted for by completed_at
        // Since each PlanExercise row is one slot (not one occurrence), we approximate:
        // missed = total past occurrences - completed
        $cumulativeMissed = max(0, $cumulativeTotal - $completedExercises);
        $completionRate   = $cumulativeTotal > 0
            ? round(($completedExercises / $cumulativeTotal) * 100)
            : 0;

        // ── Per-day stats for the bar chart (current week view) ─────────────
        $weekStart  = Carbon::now()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $weekEnd    = Carbon::now()->endOfWeek(Carbon::SUNDAY)->endOfDay();
        $todayOrder = $dayOrder[$todayName];
        $dailyStats = [];

        foreach ($days as $day) {
            $dayExercises = $planExercises->where('day_of_week', $day);
            $dayTotal     = $dayExercises->count();
            $dayDone      = $dayExercises->filter(
                fn($pe) => $pe->completed_at && $pe->completed_at->between($weekStart, $weekEnd)
            )->count();
            $dayPassed = $dayOrder[$day] < $todayOrder;
            $dayMissed = $dayPassed ? max(0, $dayTotal - $dayDone) : 0;

            $dailyStats[$day] = [
                'total'  => $dayTotal,
                'done'   => $dayDone,
                'missed' => $dayMissed,
                'rate'   => $dayTotal > 0 ? round(($dayDone / $dayTotal) * 100) : 0,
            ];
        }

        // ── Cumulative per-day completion rate (plan start → today) ──────────
        // For each weekday, total due = occurrences × slots on that day.
        // Completed = exercises on that day that have a completed_at.
        $cumulativeDayStats = [];
        foreach ($days as $day) {
            $slots        = $planExercises->where('day_of_week', $day);
            $slotCount    = $slots->count();
            $occurrences  = $dayOccurrences[$day]; // already computed above
            $totalDue     = $slotCount * $occurrences;
            $doneCount    = $slots->filter(fn($pe) => (bool) $pe->completed_at)->count();

            $cumulativeDayStats[$day] = [
                'total'     => $totalDue,
                'done'      => $doneCount,
                'rate'      => $totalDue > 0 ? round(($doneCount / $totalDue) * 100) : 0,
                'label'     => "{$doneCount}/{$totalDue}",
            ];
        }

        // Per-exercise breakdown — current status
        $exerciseStats = $planExercises->map(function ($pe) {
            return [
                'name'         => $pe->exercise->name,
                'target_area'  => $pe->exercise->target_area,
                'day'          => $pe->day_of_week,
                'completed'    => (bool) $pe->completed_at,
                'completed_at' => $pe->completed_at ? $pe->completed_at->format('M d, Y H:i') : null,
            ];
        })->values();

        // Activity heatmap — all-time completed_at counts per date
        $completedDates = $planExercises->filter(fn($pe) => $pe->completed_at)
            ->groupBy(fn($pe) => $pe->completed_at->format('Y-m-d'))
            ->map(fn($group) => $group->count())
            ->toArray();

        return view('patient.progress', [
            'activePlan'         => $activePlan,
            'totalExercises'     => $cumulativeTotal,
            'completedExercises' => $completedExercises,
            'missedExercises'    => $cumulativeMissed,
            'completionRate'     => $completionRate,
            'dailyStats'         => $dailyStats,
            'cumulativeDayStats' => $cumulativeDayStats,
            'exerciseStats'      => $exerciseStats,
            'completedDates'     => $completedDates,
            'planStart'          => $planStart,
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
            'feedback_requested' => true,
            'feedback_requested_at' => now(),
        ]);

        return redirect()->route('patient.dashboard')
            ->with('success', 'Thank you for your feedback! Your clinician will review and may recommend a new plan.');
    }

    /**
     * Standalone feedback form page — accessible any time, shows eligibility status.
     */
    public function feedbackForm()
    {
        $user = auth()->user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();
        $activePlan = $patient->rehabPlans()->where('status', 'active')->first();
        $planExercises = $activePlan ? $activePlan->exercises()->with('exercise')->get() : collect();

        $total = $planExercises->count();
        $completed = $planExercises->where('is_completed', true)->count();
        $completionRate = $total > 0 ? round(($completed / $total) * 100) : 0;
        $daysOnPlan = $activePlan && $activePlan->start_date
            ? (int) $activePlan->start_date->diffInDays(now())
            : 0;

        $eligible = $this->isEligibleForFeedback($activePlan, $planExercises);

        // Ineligibility reasons for user-facing messaging
        $reasons = [];
        if (!$activePlan) {
            $reasons[] = 'You do not have an active rehabilitation plan.';
        } else {
            if ($daysOnPlan < 30) {
                $reasons[] = "Your plan must be at least 30 days old (currently {$daysOnPlan} days).";
            }
            if ($completionRate < 60) {
                $reasons[] = "You must complete at least 60% of your exercises (currently {$completionRate}%).";
            }
        }

        return view('patient.feedback-form', [
            'activePlan'     => $activePlan,
            'planExercises'  => $planExercises,
            'eligible'       => $eligible,
            'reasons'        => $reasons,
            'completionRate' => $completionRate,
            'daysOnPlan'     => $daysOnPlan,
        ]);
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

        return $this->isEligibleForFeedback($activePlan, $planExercises);
    }

    /**
     * Pure eligibility check — no side effects.
     */
    private function isEligibleForFeedback($activePlan, $planExercises): bool
    {
        if (!$activePlan) {
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
        if (($completed / $total) < 0.6) {
            return false;
        }

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

    private function getTodaysExercises($planExercises)
    {
        $todayName = Carbon::now()->format('l'); // e.g. "Friday"

        return $planExercises
            ->filter(fn($pe) => $pe->day_of_week === $todayName)
            ->sortBy(fn($pe) => $pe->scheduled_time ?? '23:59')
            ->values();
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
        $user = auth()->user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();
        return view('patient.profile', [
            'patient' => $patient,
            'user'    => $user,
        ]);
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
