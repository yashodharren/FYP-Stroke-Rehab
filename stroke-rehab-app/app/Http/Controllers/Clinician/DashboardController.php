<?php

namespace App\Http\Controllers\Clinician;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\RehabPlan;
use App\Models\ClinicianMessage;
use Illuminate\Http\Request;

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

    public function showPatient($patientId)
    {
        $clinician = auth()->user();
        $patient = Patient::findOrFail($patientId);

        if ($patient->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized access to this patient.');
        }

        $rehabPlans = $patient->rehabPlans()->with('exercises')->get();

        return view('clinician.patients.show', [
            'patient' => $patient,
            'rehabPlans' => $rehabPlans,
        ]);
    }

    public function appointments()
    {
        return view('clinician.appointments.index');
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
}
