<?php

namespace App\Http\Controllers\Clinician;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientFeedback;
use App\Models\RehabPlan;
use App\Models\ClinicianMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DashboardController extends Controller
{
    public function index()
    {
        $clinician = auth()->user();
        $patients = Patient::where('clinician_id', $clinician->id)->get();
        $rehabPlans = RehabPlan::where('clinician_id', $clinician->id)->get();
        $activePlans = $rehabPlans->where('status', 'active')->count();
        $completedPlans = $rehabPlans->where('status', 'completed')->count();
        $messages = ClinicianMessage::where('clinician_id', $clinician->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('clinician.dashboard', [
            'patients' => $patients,
            'rehabPlans' => $rehabPlans,
            'activePlans' => $activePlans,
            'completedPlans' => $completedPlans,
            'messages' => $messages,
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
