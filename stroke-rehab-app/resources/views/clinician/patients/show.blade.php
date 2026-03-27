@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('clinician.patients.index') }}" class="text-blue-600 hover:text-blue-800 font-medium mb-4 inline-block">← Back to Patients</a>
            <h1 class="text-4xl font-bold text-gray-900">{{ $patient->user->name }}</h1>
            <p class="text-gray-600 mt-2">Patient Details & Rehabilitation Plans</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Age</p>
                <p class="text-2xl font-bold text-gray-900">{{ $patient->age }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Stroke Type</p>
                <p class="text-2xl font-bold text-gray-900">{{ ucfirst($patient->stroke_type) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Deficit Area</p>
                <p class="text-2xl font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $patient->deficit_area)) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm font-medium">Recovery Status</p>
                <p class="text-2xl font-bold text-gray-900">{{ ucfirst(str_replace('_', ' ', $patient->recovery_status)) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Medical History</h2>
            <p class="text-gray-700">{{ $patient->medical_history ?? 'No medical history recorded.' }}</p>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Rehabilitation Plans</h2>
                <a href="{{ route('clinician.plans.create', $patient->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
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
@endsection
