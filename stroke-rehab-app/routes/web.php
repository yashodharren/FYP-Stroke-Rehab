<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Clinician\DashboardController as ClinicianDashboardController;
use App\Http\Controllers\Clinician\PlanGeneratorController;
use App\Http\Controllers\Clinician\PatientManagementController;
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use Illuminate\Support\Facades\Auth;

Route::redirect('/', '/dashboard');

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::get('/signup', [AuthController::class, 'showSignupRole'])->name('signup.role')->middleware('guest');
Route::get('/signup/patient', [AuthController::class, 'showPatientSignup'])->name('signup.patient')->middleware('guest');
Route::post('/signup/patient', [AuthController::class, 'registerPatient'])->name('register.patient')->middleware('guest');
Route::get('/signup/clinician', [AuthController::class, 'showClinicianSignup'])->name('signup.clinician')->middleware('guest');
Route::post('/signup/clinician', [AuthController::class, 'registerClinician'])->name('register.clinician')->middleware('guest');

Route::post('/login', function () {
    $credentials = request()->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        request()->session()->regenerate();
        return redirect('/dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->middleware('guest');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return redirect('/admin');
        } elseif ($user->role === 'clinician') {
            return redirect('/clinician/dashboard');
        } elseif ($user->role === 'patient') {
            return redirect('/patient/dashboard');
        }

        abort(403, 'Unknown role');
    });

    Route::get('/test-role', function () {
        $user = auth()->user();
        return response()->json([
            'user' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'accessible_routes' => [
                'admin' => $user->role === 'admin' ? '/admin' : 'Not accessible',
                'clinician_dashboard' => $user->role === 'clinician' ? '/clinician/dashboard' : 'Not accessible',
                'patient_dashboard' => $user->role === 'patient' ? '/patient/dashboard' : 'Not accessible',
            ]
        ]);
    });

    Route::prefix('clinician')->name('clinician.')->middleware('clinician')->group(function () {
        Route::get('/dashboard', [ClinicianDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('patients')->name('patients.')->group(function () {
            Route::get('/', [PatientManagementController::class, 'index'])->name('index');
            Route::get('/create', [PatientManagementController::class, 'createForm'])->name('create');
            Route::post('/store', [PatientManagementController::class, 'store'])->name('store');
            Route::post('/{userId}/assign', [PatientManagementController::class, 'assign'])->name('assign');
            Route::delete('/{patient}/remove', [PatientManagementController::class, 'remove'])->name('remove');
            Route::get('/{patient}/edit', [PatientManagementController::class, 'editForm'])->name('edit');
            Route::put('/{patient}', [PatientManagementController::class, 'update'])->name('update');
            Route::get('/{patient}', [ClinicianDashboardController::class, 'showPatient'])->name('show');
        });

        Route::prefix('plans')->name('plans.')->group(function () {
            Route::get('/create/{patient}', [PlanGeneratorController::class, 'create'])->name('create');
            Route::post('/store/{patient}', [PlanGeneratorController::class, 'store'])->name('store');
            Route::get('/{plan}/edit', [PlanGeneratorController::class, 'edit'])->name('edit');
            Route::post('/{plan}/add-exercise', [PlanGeneratorController::class, 'addExercise'])->name('add-exercise');
            Route::put('/{plan}/exercises/{planExercise}/update', [PlanGeneratorController::class, 'updateExercise'])->name('update-exercise');
            Route::delete('/{planExercise}/remove', [PlanGeneratorController::class, 'removeExercise'])->name('remove-exercise');
            Route::delete('/{plan}/delete', [PlanGeneratorController::class, 'deletePlan'])->name('delete');
            Route::post('/{plan}/publish', [PlanGeneratorController::class, 'publish'])->name('publish');
            Route::post('/{plan}/status', [PlanGeneratorController::class, 'updateStatus'])->name('update-status');
        });

        Route::prefix('messages')->name('messages.')->group(function () {
            Route::delete('/{message}', [ClinicianDashboardController::class, 'deleteMessage'])->name('delete');
        });

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ClinicianDashboardController::class, 'showProfile'])->name('show');
            Route::put('/update', [ClinicianDashboardController::class, 'updateProfile'])->name('update');
            Route::put('/change-password', [ClinicianDashboardController::class, 'changePassword'])->name('change-password');
        });
    });

    Route::prefix('patient')->name('patient.')->middleware('patient')->group(function () {
        Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
        Route::get('/details', [PatientDashboardController::class, 'details'])->name('details');
        Route::get('/schedule', [PatientDashboardController::class, 'schedule'])->name('schedule');
        Route::post('/feedback/{planExercise}', [PatientDashboardController::class, 'submitFeedback'])->name('feedback');
        Route::post('/reschedule/{planExercise}', [PatientDashboardController::class, 'rescheduleExercise'])->name('reschedule');
        Route::post('/mark-done/{planExercise}', [PatientDashboardController::class, 'markDone'])->name('mark-done');
        Route::post('/plan-feedback/{plan}', [PatientDashboardController::class, 'submitPlanFeedback'])->name('plan-feedback');

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [PatientDashboardController::class, 'showProfile'])->name('show');
            Route::put('/update', [PatientDashboardController::class, 'updateProfile'])->name('update');
            Route::put('/change-password', [PatientDashboardController::class, 'changePassword'])->name('change-password');
        });
    });
});
