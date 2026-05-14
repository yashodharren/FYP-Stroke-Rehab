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

    @if($planFeedback->isNotEmpty())
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-white">Patient Feedback</h2>
                <p class="text-amber-100 text-sm mt-1">Submitted after 1 month of active rehabilitation</p>
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($planFeedback as $planId => $feedbackItems)
            @php
            $planForFeedback = $rehabPlans->firstWhere('id', $planId);
            $avgPain = round($feedbackItems->avg('pain_level'), 1);
            $avgDifficulty = round($feedbackItems->avg('difficulty_rating'), 1);
            $overallComment = $feedbackItems->first()->overall_comments;
            $feedbackDate = $feedbackItems->first()->feedback_date;
            @endphp
            <div class="px-6 py-5">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $planForFeedback ? $planForFeedback->plan_name : 'Plan #'.$planId }}</h3>
                        <p class="text-gray-500 text-sm mt-1">Submitted {{ $feedbackDate ? \Carbon\Carbon::parse($feedbackDate)->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div class="flex gap-4 text-sm">
                        <div class="text-center">
                            <p class="text-gray-500 text-xs">Avg Pain</p>
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $avgPain >= 7 ? 'bg-red-100 text-red-800' : ($avgPain >= 4 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ $avgPain }}/10
                            </span>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-500 text-xs">Avg Difficulty</p>
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $avgDifficulty >= 4 ? 'bg-red-100 text-red-800' : ($avgDifficulty >= 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ $avgDifficulty }}/5
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
                    @foreach($feedbackItems as $fb)
                    @if($fb->planExercise && $fb->planExercise->exercise)
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <p class="font-medium text-gray-900 text-sm">{{ $fb->planExercise->exercise->name }}</p>
                        <div class="flex gap-3 mt-2 text-xs text-gray-600">
                            <span class="flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full {{ ($fb->pain_level ?? 0) >= 7 ? 'bg-red-500' : (($fb->pain_level ?? 0) >= 4 ? 'bg-yellow-500' : 'bg-green-500') }}"></span>
                                Pain: {{ $fb->pain_level ?? 'N/A' }}/10
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full {{ ($fb->difficulty_rating ?? 0) >= 4 ? 'bg-red-500' : (($fb->difficulty_rating ?? 0) >= 3 ? 'bg-yellow-500' : 'bg-green-500') }}"></span>
                                Difficulty: {{ $fb->difficulty_rating ?? 'N/A' }}/5
                            </span>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>

                @if($overallComment)
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
                    <p class="text-sm text-amber-900"><strong>Patient Comment:</strong> {{ $overallComment }}</p>
                </div>
                @endif

                @php
                $alreadyHasNewPlan = $rehabPlans->where('id', '!=', $planId)
                ->where('created_at', '>', optional($planForFeedback)->created_at)
                ->isNotEmpty();
                @endphp
                @if(!$alreadyHasNewPlan)
                <a href="{{ route('clinician.plans.create', $patient->id) }}?from_feedback={{ $planId }}"
                    class="inline-flex items-center gap-2 bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 font-medium text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Generate New Recommended Plan
                </a>
                @else
                <span class="inline-flex items-center gap-2 text-green-700 text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    New plan already created
                </span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

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
                        <form method="POST" action="{{ route('clinician.plans.update-status', $plan->id) }}" class="inline">
                            @csrf
                            <select name="status" data-original-status="{{ $plan->status }}" onchange="confirmStatusChange(this, '{{ $plan->plan_name }}')"
                                class="px-3 py-1 rounded-full text-xs font-medium border-0 cursor-pointer focus:ring-2 focus:ring-offset-1
                                @if($plan->status === 'draft') bg-gray-100 text-gray-800 focus:ring-gray-400
                                @elseif($plan->status === 'active') bg-green-100 text-green-800 focus:ring-green-400
                                @elseif($plan->status === 'completed') bg-blue-100 text-blue-800 focus:ring-blue-400
                                @else bg-yellow-100 text-yellow-800 focus:ring-yellow-400
                                @endif">
                                <option value="draft" @selected($plan->status === 'draft')>Draft</option>
                                <option value="active" @selected($plan->status === 'active')>Active</option>
                                <option value="paused" @selected($plan->status === 'paused')>Paused</option>
                                <option value="completed" @selected($plan->status === 'completed')>Completed</option>
                            </select>
                        </form>
                        <div class="mt-3 space-y-2">
                            <a href="{{ route('clinician.plans.edit', $plan->id) }}" class="block text-blue-600 hover:text-blue-800 font-medium text-sm">Edit</a>
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
    function confirmStatusChange(selectEl, planName) {
        const newStatus = selectEl.value;
        const originalStatus = selectEl.dataset.originalStatus;
        Swal.fire({
            title: 'Change plan status?',
            html: `Set "<strong>${planName}</strong>" status to <strong>${newStatus}</strong>?` +
                (newStatus === 'active' ? '<br><br><span class="text-yellow-600 text-sm">Any other active plan for this patient will be paused.</span>' : ''),
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, update',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                selectEl.form.submit();
            } else if (originalStatus) {
                selectEl.value = originalStatus;
            }
        });
    }

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