@extends('layouts.patient')

@section('page_title', 'Profile')

@section('content')
@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@php
$genderMap = [0 => 'Female', 1 => 'Male', 2 => 'Other'];
$deficits = [];
if ($patient->rdef1) $deficits[] = 'Motor Deficit (Right)';
if ($patient->rdef2) $deficits[] = 'Sensory Deficit (Right)';
if ($patient->rdef3) $deficits[] = 'Vision Deficit (Right)';
if ($patient->rdef4) $deficits[] = 'Speech Deficit';
if ($patient->rdef5) $deficits[] = 'Cognitive Deficit';
if ($patient->rdef6) $deficits[] = 'Emotional Deficit';
if ($patient->rdef7) $deficits[] = 'Swallowing Deficit';
if ($patient->rdef8) $deficits[] = 'Urinary Deficit';
@endphp

{{-- Patient Info + Medical Info --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

    <!-- Patient Information (read-only) -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-500 to-teal-500">
            <h2 class="text-xl font-bold text-white">Patient Information</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Full Name</p>
                <p class="text-gray-900 font-semibold text-base mt-1">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Email Address</p>
                <p class="text-gray-900 font-semibold text-base mt-1">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Age</p>
                <p class="text-gray-900 font-semibold text-base mt-1">{{ $patient->age ?? 'Not specified' }} {{ $patient->age ? 'years' : '' }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Gender</p>
                <p class="text-gray-900 font-semibold text-base mt-1">{{ $genderMap[$patient->gender] ?? 'Not specified' }}</p>
            </div>
        </div>
    </div>

    <!-- Medical Information (read-only) -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-500 to-teal-500">
            <h2 class="text-xl font-bold text-white">Medical Information</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Stroke Type</p>
                <p class="text-gray-900 font-semibold text-base mt-1">{{ ucfirst(str_replace('_', ' ', $patient->stroke_subtype ?? 'Not specified')) }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Recovery Status</p>
                <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-semibold
                    @if($patient->recovery_status === 'excellent') bg-green-100 text-green-800
                    @elseif($patient->recovery_status === 'good') bg-blue-100 text-blue-800
                    @elseif($patient->recovery_status === 'fair') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($patient->recovery_status ?? 'Not specified') }}
                </span>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Conscious State</p>
                <p class="text-gray-900 font-semibold text-base mt-1">{{ ucfirst($patient->conscious_state ?? 'Not specified') }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">Systolic BP (mmHg)</p>
                <p class="text-gray-900 font-semibold text-base mt-1">{{ $patient->rsbp ?? 'Not recorded' }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Functional Deficits --}}
<div class="bg-white rounded-lg shadow overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-500 to-teal-500">
        <h2 class="text-xl font-bold text-white">Functional Deficits</h2>
    </div>
    <div class="p-6">
        @if(count($deficits) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($deficits as $deficit)
            <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium text-gray-900 text-sm">{{ $deficit }}</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-6">
            <svg class="w-10 h-10 text-green-400 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <p class="text-gray-600 font-medium">No functional deficits identified</p>
        </div>
        @endif
    </div>
</div>

{{-- Account Settings --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Update Name/Email -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-500 to-teal-500">
            <h2 class="text-xl font-bold text-white">Account Settings</h2>
        </div>
        <form method="POST" action="{{ route('patient.profile.update') }}" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                    @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-500 to-teal-500">
            <h2 class="text-xl font-bold text-white">Change Password</h2>
        </div>
        <form method="POST" action="{{ route('patient.profile.change-password') }}" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input type="password" id="current_password" name="current_password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                    @error('current_password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                    @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    <p class="text-gray-500 text-xs mt-1">Minimum 8 characters</p>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                    @error('password_confirmation')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection