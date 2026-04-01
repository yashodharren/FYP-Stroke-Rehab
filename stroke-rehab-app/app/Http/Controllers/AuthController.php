<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Clinician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show sign-up role selection page
     */
    public function showSignupRole()
    {
        return view('auth.signup-role');
    }

    /**
     * Show patient sign-up form
     */
    public function showPatientSignup()
    {
        return view('auth.signup-patient');
    }

    /**
     * Show clinician sign-up form
     */
    public function showClinicianSignup()
    {
        return view('auth.signup-clinician');
    }

    /**
     * Register a new patient
     */
    public function registerPatient(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'age' => 'required|integer|min:0|max:150',
            'gender' => 'required|in:0,1',
            'rsbp' => 'nullable|integer|min:0|max:300',
            'stroke_subtype' => 'nullable|in:TACS,PACS,LACS,POCS,OTH',
            'conscious_state' => 'nullable|in:Alert,Drowsy,Unconscious',
            'rdef1' => 'nullable|boolean',
            'rdef2' => 'nullable|boolean',
            'rdef3' => 'nullable|boolean',
            'rdef4' => 'nullable|boolean',
            'rdef5' => 'nullable|boolean',
            'rdef6' => 'nullable|boolean',
            'rdef7' => 'nullable|boolean',
            'rdef8' => 'nullable|boolean',
        ]);

        // Create user account
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'patient',
        ]);

        // Create patient record
        $patient = Patient::create([
            'user_id' => $user->id,
            'age' => $validated['age'],
            'gender' => $validated['gender'],
            'rsbp' => $validated['rsbp'],
            'stroke_subtype' => $validated['stroke_subtype'],
            'conscious_state' => $validated['conscious_state'],
            'rdef1' => $validated['rdef1'] ?? false,
            'rdef2' => $validated['rdef2'] ?? false,
            'rdef3' => $validated['rdef3'] ?? false,
            'rdef4' => $validated['rdef4'] ?? false,
            'rdef5' => $validated['rdef5'] ?? false,
            'rdef6' => $validated['rdef6'] ?? false,
            'rdef7' => $validated['rdef7'] ?? false,
            'rdef8' => $validated['rdef8'] ?? false,
            'recovery_status' => 'new',
        ]);

        // Log the user in
        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Welcome! Your patient account has been created successfully.');
    }

    /**
     * Register a new clinician
     */
    public function registerClinician(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'specialization' => 'required|string|max:255',
            'hospital_affiliation' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        // Create user account
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'clinician',
        ]);

        // Create clinician record
        $clinician = Clinician::create([
            'user_id' => $user->id,
            'license_number' => $validated['license_number'],
            'specialization' => $validated['specialization'],
            'hospital_affiliation' => $validated['hospital_affiliation'],
            'phone' => $validated['phone'],
            'is_verified' => false,
        ]);

        // Log the user in
        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Welcome! Your clinician account has been created. Please wait for admin verification.');
    }
}
