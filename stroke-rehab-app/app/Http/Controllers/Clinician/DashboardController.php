<?php

namespace App\Http\Controllers\Clinician;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientFeedback;
use App\Models\PlanExercise;
use App\Models\RehabPlan;
use App\Models\ClinicianMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DashboardController extends Controller
{
    public function index()
    {
        $clinician = auth()->user();
        $patients  = Patient::where('clinician_id', $clinician->id)->with('user')->get();
        $patientIds = $patients->pluck('id');

        $rehabPlans     = RehabPlan::where('clinician_id', $clinician->id)->get();
        $activePlans    = $rehabPlans->where('status', 'active')->count();
        $completedPlans = $rehabPlans->where('status', 'completed')->count();
        $draftPlans     = $rehabPlans->where('status', 'draft')->count();

        $messages = ClinicianMessage::where('clinician_id', $clinician->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Recent feedback submissions (latest session per patient, last 7 days)
        $recentFeedback = PatientFeedback::whereIn('patient_id', $patientIds)
            ->where('is_plan_feedback', true)
            ->where('feedback_date', '>=', Carbon::now()->subDays(7))
            ->with(['patient.user', 'rehabPlan'])
            ->orderByDesc('feedback_date')
            ->get()
            ->groupBy(fn($fb) => Carbon::parse($fb->feedback_date)->format('Y-m-d H:i') . '_' . $fb->patient_id)
            ->map(fn($group) => $group->first())
            ->take(5)
            ->values();

        // Weekly exercise adherence across all active plans
        $todayName   = Carbon::now()->format('l');
        $weekStart   = Carbon::now()->startOfWeek();
        $weekDays    = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $activePlanIds = $rehabPlans->where('status', 'active')->pluck('id');

        $weeklyExercises = PlanExercise::whereIn('rehab_plan_id', $activePlanIds)->get();
        $totalWeek       = $weeklyExercises->count();
        $completedWeek   = $weeklyExercises->where('is_completed', true)->count();
        $adherencePct    = $totalWeek > 0 ? round(($completedWeek / $totalWeek) * 100) : 0;

        // Patients without any active plan
        $activePatientIds = $rehabPlans->where('status', 'active')->pluck('patient_id')->unique();
        $patientsNoActivePlan = $patients->filter(fn($p) => !$activePatientIds->contains($p->id));

        // Per-patient quick stats
        $patientStats = $patients->map(function ($p) use ($rehabPlans) {
            $plans       = $rehabPlans->where('patient_id', $p->id);
            $activePlan  = $plans->where('status', 'active')->first();
            $exercises   = $activePlan ? PlanExercise::where('rehab_plan_id', $activePlan->id)->get() : collect();
            $done        = $exercises->where('is_completed', true)->count();
            $total       = $exercises->count();
            return [
                'patient'     => $p,
                'active_plan' => $activePlan,
                'done'        => $done,
                'total'       => $total,
                'pct'         => $total > 0 ? round(($done / $total) * 100) : 0,
            ];
        })->sortByDesc('pct')->take(6)->values();

        return view('clinician.dashboard', [
            'patients'             => $patients,
            'rehabPlans'           => $rehabPlans,
            'activePlans'          => $activePlans,
            'completedPlans'       => $completedPlans,
            'draftPlans'           => $draftPlans,
            'messages'             => $messages,
            'recentFeedback'       => $recentFeedback,
            'adherencePct'         => $adherencePct,
            'completedWeek'        => $completedWeek,
            'totalWeek'            => $totalWeek,
            'patientsNoActivePlan' => $patientsNoActivePlan,
            'patientStats'         => $patientStats,
        ]);
    }

    public function feedbackIndex()
    {
        $clinician = auth()->user();

        $feedbackByPatient = PatientFeedback::whereHas('patient', function ($q) use ($clinician) {
            $q->where('clinician_id', $clinician->id);
        })
            ->where('is_plan_feedback', true)
            ->with(['patient.user', 'planExercise.exercise', 'rehabPlan'])
            ->orderByDesc('feedback_date')
            ->get()
            ->groupBy('patient_id');

        return view('clinician.feedback.index', [
            'feedbackByPatient' => $feedbackByPatient,
        ]);
    }

    public function deleteFeedbackSession(Request $request)
    {
        $clinician = auth()->user();

        $validated = $request->validate([
            'patient_id'   => 'required|integer',
            'plan_id'      => 'required|integer',
            'session_key'  => 'required|string', // "Y-m-d H:i" minute-level key
        ]);

        // Verify the patient belongs to this clinician
        $patient = Patient::where('id', $validated['patient_id'])
            ->where('clinician_id', $clinician->id)
            ->firstOrFail();

        // Load all feedback for this patient+plan, filter by session minute
        $toDelete = PatientFeedback::where('patient_id', $patient->id)
            ->where('rehab_plan_id', $validated['plan_id'])
            ->where('is_plan_feedback', true)
            ->get()
            ->filter(fn($fb) => $fb->feedback_date &&
                \Carbon\Carbon::parse($fb->feedback_date)->format('Y-m-d H:i') === $validated['session_key']);

        $count = $toDelete->count();
        PatientFeedback::destroy($toDelete->pluck('id')->all());

        return redirect()->route('clinician.feedback.index')
            ->with('success', "Deleted {$count} feedback record(s) from that submission.");
    }

    public function showPatient($patientId)
    {
        $clinician = auth()->user();
        $patient = Patient::findOrFail($patientId);

        if ($patient->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized access to this patient.');
        }

        $rehabPlans = $patient->rehabPlans()->with('exercises')->get();

        // Fetch plan-level feedback grouped by plan
        $planFeedback = PatientFeedback::where('patient_id', $patient->id)
            ->where('is_plan_feedback', true)
            ->with('planExercise.exercise')
            ->orderByDesc('feedback_date')
            ->get()
            ->groupBy('rehab_plan_id');

        return view('clinician.patients.show', [
            'patient' => $patient,
            'rehabPlans' => $rehabPlans,
            'planFeedback' => $planFeedback,
        ]);
    }

    public function deleteMessage($messageId)
    {
        $clinician = auth()->user();
        $message = ClinicianMessage::findOrFail($messageId);

        if ($message->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized to delete this message.');
        }

        $message->delete();

        return redirect()->route('clinician.dashboard')
            ->with('success', 'Message deleted successfully.');
    }

    public function showProfile()
    {
        return view('clinician.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('clinician.profile.show')
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

        return redirect()->route('clinician.profile.show')
            ->with('success', 'Password changed successfully.');
    }
}
