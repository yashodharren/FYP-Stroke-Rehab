@extends('layouts.patient')

@section('title', 'My Details')
@section('page_title', 'My Details')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <!-- Personal Information -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-500 to-teal-500">
            <h2 class="text-xl font-bold text-white">Personal Information</h2>
        </div>
        <div class="p-6 space-y-6">
            <div>
                <p class="text-gray-600 text-sm font-medium">Full Name</p>
                <p class="text-gray-900 font-semibold text-lg mt-1">{{ $user->name }}</p>
            </div>

            <div>
                <p class="text-gray-600 text-sm font-medium">Email Address</p>
                <p class="text-gray-900 font-semibold text-lg mt-1">{{ $user->email }}</p>
            </div>

            <div>
                <p class="text-gray-600 text-sm font-medium">Age</p>
                <p class="text-gray-900 font-semibold text-lg mt-1">{{ $patient->age ?? 'Not specified' }} years</p>
            </div>

            <div>
                <p class="text-gray-600 text-sm font-medium">Gender</p>
                <p class="text-gray-900 font-semibold text-lg mt-1">
                    @php
                    $genderMap = [
                    0 => 'Female',
                    1 => 'Male',
                    2 => 'Other'
                    ];
                    $genderLabel = $genderMap[$patient->gender] ?? 'Not specified';
                    @endphp
                    {{ $genderLabel }}
                </p>
            </div>
        </div>
    </div>

    <!-- Medical Information -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-500 to-teal-500">
            <h2 class="text-xl font-bold text-white">Medical Information</h2>
        </div>
        <div class="p-6 space-y-6">
            <div>
                <p class="text-gray-600 text-sm font-medium">Stroke Type</p>
                <p class="text-gray-900 font-semibold text-lg mt-1">{{ ucfirst(str_replace('_', ' ', $patient->stroke_subtype ?? 'Not specified')) }}</p>
            </div>

            <div>
                <p class="text-gray-600 text-sm font-medium">Recovery Status</p>
                <div class="mt-1">
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                        @if($patient->recovery_status === 'excellent') bg-green-100 text-green-800
                        @elseif($patient->recovery_status === 'good') bg-blue-100 text-blue-800
                        @elseif($patient->recovery_status === 'fair') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($patient->recovery_status ?? 'Not specified') }}
                    </span>
                </div>
            </div>

            <div>
                <p class="text-gray-600 text-sm font-medium">Conscious State</p>
                <p class="text-gray-900 font-semibold text-lg mt-1">{{ ucfirst($patient->conscious_state ?? 'Not specified') }}</p>
            </div>

            <div>
                <p class="text-gray-600 text-sm font-medium">Systolic Blood Pressure (mmHg)</p>
                <p class="text-gray-900 font-semibold text-lg mt-1">{{ $patient->rsbp ?? 'Not recorded' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Rehabilitation Deficits -->
<div class="mt-8 bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-500 to-teal-500">
        <h2 class="text-xl font-bold text-white">Rehabilitation Deficits</h2>
    </div>
    <div class="p-6">
        @php
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

        @if(count($deficits) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($deficits as $deficit)
            <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <span class="font-medium text-gray-900">{{ $deficit }}</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <svg class="w-12 h-12 text-green-500 mx-auto mb-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <p class="text-gray-600 font-medium">No functional deficits identified</p>
            <p class="text-gray-500 text-sm mt-1">Great progress in your recovery!</p>
        </div>
        @endif
    </div>
</div>
@endsection