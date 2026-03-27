<?php

namespace App\Http\Controllers\Clinician;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\RehabPlan;
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

        return view('clinician.dashboard', [
            'patients' => $patients,
            'rehabPlans' => $rehabPlans,
            'activePlans' => $activePlans,
            'completedPlans' => $completedPlans,
        ]);
    }

    public function patients()
    {
        $clinician = auth()->user();
        $patients = Patient::where('clinician_id', $clinician->id)->with('user')->get();

        return view('clinician.patients.index', ['patients' => $patients]);
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
}
