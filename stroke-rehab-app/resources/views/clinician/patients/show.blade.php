@extends('layouts.clinician')

@section('page_title', 'Patient Details')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-start">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('clinician.patients.index') }}" class="text-blue-600 hover:text-blue-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">{{ $patient->user->name }}</h1>
            </div>
            <p class="text-gray-600 mt-2">Patient Details & Rehabilitation Plans</p>
        </div>
        <button onclick="deletePatient({{ $patient->id }})" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 font-medium">
            Delete Patient
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Age</p>
            <p class="text-2xl font-bold text-gray-900">{{ $patient->age }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Gender</p>
            <p class="text-2xl font-bold text-gray-900">{{ $patient->gender === 0 ? 'Female' : 'Male' }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Systolic Blood Pressure</p>
            <p class="text-2xl font-bold text-gray-900">{{ $patient->rsbp ?? 'N/A' }} mmHg</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Stroke Type</p>
            <p class="text-2xl font-bold text-gray-900">{{ $patient->stroke_subtype ?? 'N/A' }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Consciousness State</p>
            <p class="text-2xl font-bold text-gray-900">{{ $patient->conscious_state ?? 'N/A' }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Recovery Status</p>
            <p class="text-2xl font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $patient->recovery_status)) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Functional Deficits</h2>
        <div class="text-sm text-gray-900 space-y-2">
            @php
            $deficits = [];
            if($patient->rdef1) $deficits[] = 'Face';
            if($patient->rdef2) $deficits[] = 'Arm/Hand';
            if($patient->rdef3) $deficits[] = 'Leg/Foot';
            if($patient->rdef4) $deficits[] = 'Speech';
            if($patient->rdef5) $deficits[] = 'Vision';
            if($patient->rdef6) $deficits[] = 'Visuospatial';
            if($patient->rdef7) $deficits[] = 'Brainstem';
            if($patient->rdef8) $deficits[] = 'Other';
            @endphp
            @if(count($deficits) > 0)
            <div class="flex flex-wrap gap-2">
                @foreach($deficits as $deficit)
                <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">{{ $deficit }}</span>
                @endforeach
            </div>
            @else
            <p class="text-gray-600">No functional deficits recorded</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gradient-to-r from-cyan-500 to-blue-500 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-white">Rehabilitation Plans</h2>
            <a href="{{ route('clinician.plans.create', $patient->id) }}" class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-800 font-medium">
                Create New Plan
            </a>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($rehabPlans as $plan)
            <div class="px-6 py-4 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $plan->plan_name }}</h3>
                        <p class="text-gray-600 text-sm mt-1">{{ $plan->description }}</p>
                        <div class="mt-3 flex gap-4 text-sm text-gray-600">
                            <span>📅 {{ $plan->start_date->format('M d, Y') }} - {{ $plan->end_date ? $plan->end_date->format('M d, Y') : 'Ongoing' }}</span>
                            <span>📊 Difficulty: {{ $plan->difficulty_level }}/5</span>
                            <span>📈 Recovery: {{ ($plan->recovery_probability * 100) ?? 'N/A' }}%</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                    @if($plan->status === 'draft') bg-gray-100 text-gray-800
                                    @elseif($plan->status === 'active') bg-green-100 text-green-800
                                    @elseif($plan->status === 'completed') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                            {{ ucfirst($plan->status) }}
                        </span>
                        <div class="mt-3 space-y-2">
                            <a href="{{ route('clinician.plans.edit', $plan->id) }}" class="block text-blue-600 hover:text-blue-800 font-medium text-sm">Edit</a>
                            @if($plan->status === 'draft')
                            <form method="POST" action="{{ route('clinician.plans.publish', $plan->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800 font-medium text-sm">Publish</button>
                            </form>
                            @endif
                            <button type="button" onclick="deletePlan({{ $plan->id }}, '{{ $plan->plan_name }}')" class="block text-red-600 hover:text-red-800 font-medium text-sm">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-gray-600">
                <p>No rehabilitation plans created yet.</p>
                <a href="{{ route('clinician.plans.create', $patient->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">Create the first plan</a>
            </div>
            @endforelse
        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deletePatient(patientId) {
        Swal.fire({
            title: 'Delete Patient?',
            text: 'Are you sure you want to delete this patient from your care? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete Patient',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/clinician/patients/' + patientId + '/remove';
                form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                    '<input type="hidden" name="_method" value="DELETE">';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function deletePlan(planId, planName) {
        Swal.fire({
            title: 'Delete Rehabilitation Plan?',
            text: 'Are you sure you want to delete "' + planName + '"? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete Plan',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/clinician/plans/' + planId + '/delete';
                form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                    '<input type="hidden" name="_method" value="DELETE">';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection