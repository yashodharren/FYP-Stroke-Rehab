<?php

namespace App\Http\Controllers\Clinician;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PatientManagementController extends Controller
{
    /**
     * Show patient management page with search and add options
     */
    public function index(Request $request)
    {
        $clinician = auth()->user();
        $search = $request->input('search');
        $patients = Patient::where('clinician_id', $clinician->id)->with('user')->get();
        $unassignedPatients = [];

        if ($search) {
            $unassignedPatients = User::where('role', 'patient')
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->whereDoesntHave('patientProfile', function ($query) use ($clinician) {
                    $query->where('clinician_id', $clinician->id);
                })
                ->get();
        }

        return view('clinician.patients.index', [
            'patients' => $patients,
            'unassignedPatients' => $unassignedPatients,
            'search' => $search,
        ]);
    }

    /**
     * Show form to create new patient user account
     */
    public function createForm()
    {
        return view('clinician.patients.create');
    }

    /**
     * Store new patient user account and create patient record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|in:0,1',
            'rsbp' => 'nullable|integer|min:0|max:300',
            'stroke_subtype' => 'nullable|in:TACS,PACS,LACS,POCS,OTH',
            'conscious_state' => 'nullable|in:Alert,Drowsy,Unconscious',
        ]);

        // Generate temporary password
        $tempPassword = Str::random(12);

        // Create user account
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'role' => 'patient',
        ]);

        // Create patient record
        $patientData = [
            'user_id' => $user->id,
            'clinician_id' => auth()->id(),
            'age' => $validated['age'],
            'gender' => $validated['gender'],
            'rsbp' => $validated['rsbp'],
            'stroke_subtype' => $validated['stroke_subtype'],
            'conscious_state' => $validated['conscious_state'],
            'recovery_status' => 'new',
        ];

        $patient = Patient::create($patientData);

        return redirect()->route('clinician.patients.index')
            ->with('success', "Patient '{$user->name}' created successfully. Temporary password: {$tempPassword}");
    }

    /**
     * Assign existing patient to clinician
     */
    public function assign(Request $request, $userId)
    {
        $clinician = auth()->user();
        $user = User::findOrFail($userId);

        if ($user->role !== 'patient') {
            return redirect()->back()->with('error', 'User is not a patient.');
        }

        // Check if patient already has a clinician
        $existingPatient = Patient::where('user_id', $userId)->first();
        if ($existingPatient && $existingPatient->clinician_id !== null) {
            return redirect()->back()->with('error', 'Patient is already assigned to another clinician.');
        }

        // Create or update patient record
        if ($existingPatient) {
            $existingPatient->update(['clinician_id' => $clinician->id]);
        } else {
            Patient::create([
                'user_id' => $userId,
                'clinician_id' => $clinician->id,
                'age' => 0,
                'recovery_status' => 'new',
            ]);
        }

        return redirect()->route('clinician.patients.index')
            ->with('success', "Patient '{$user->name}' assigned to your care.");
    }

    /**
     * Remove patient from clinician's care
     */
    public function remove($patientId)
    {
        $clinician = auth()->user();
        $patient = Patient::findOrFail($patientId);

        if ($patient->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized access to this patient.');
        }

        $patientName = $patient->user->name;
        $patient->update(['clinician_id' => null]);

        return redirect()->route('clinician.patients.index')
            ->with('success', "Patient '{$patientName}' removed from your care.");
    }

    /**
     * Show form to edit patient clinical information
     */
    public function editForm($patientId)
    {
        $clinician = auth()->user();
        $patient = Patient::findOrFail($patientId);

        if ($patient->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized access to this patient.');
        }

        return view('clinician.patients.edit', ['patient' => $patient]);
    }

    /**
     * Update patient clinical information
     */
    public function update(Request $request, $patientId)
    {
        $clinician = auth()->user();
        $patient = Patient::findOrFail($patientId);

        if ($patient->clinician_id !== $clinician->id) {
            abort(403, 'Unauthorized access to this patient.');
        }

        $validated = $request->validate([
            'age' => 'required|integer|min:0|max:150',
            'gender' => 'required|in:0,1',
            'rsbp' => 'nullable|integer|min:0|max:300',
            'stroke_subtype' => 'required|in:TACS,PACS,LACS,POCS,OTH',
            'conscious_state' => 'required|in:Alert,Drowsy,Unconscious',
            'rdef1' => 'boolean',
            'rdef2' => 'boolean',
            'rdef3' => 'boolean',
            'rdef4' => 'boolean',
            'rdef5' => 'boolean',
            'rdef6' => 'boolean',
            'rdef7' => 'boolean',
            'rdef8' => 'boolean',
            'recovery_status' => 'required|in:new,in_progress,completed,paused',
        ]);

        $patient->update($validated);

        return redirect()->route('clinician.patients.index')
            ->with('success', "Patient '{$patient->user->name}' information updated successfully.");
    }
}
