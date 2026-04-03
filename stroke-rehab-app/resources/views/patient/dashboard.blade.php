@extends('layouts.patient')

@section('title', 'Patient Dashboard')
@section('page_title', 'My Rehabilitation Plan')

@section('content')
@if($activePlan)
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-medium">Plan Name</p>
        <p class="text-2xl font-bold text-gray-900">{{ $activePlan->plan_name }}</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-medium">Difficulty Level</p>
        <p class="text-2xl font-bold text-gray-900">{{ $activePlan->difficulty_level }}/5</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-medium">Total Exercises</p>
        <p class="text-2xl font-bold text-gray-900">{{ $planExercises->count() }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">Weekly Schedule</h2>
    </div>
    <div class="p-6">
        <a href="{{ route('patient.schedule') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">
            View Full Schedule
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">Next 24 Hours - Upcoming Exercises</h2>
    </div>
    <div class="divide-y divide-gray-200">
        @forelse($upcomingExercises as $planExercise)
        <div class="px-6 py-4 hover:bg-gray-50">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $planExercise->exercise->name }}</h3>
                    <p class="text-gray-600 text-sm mt-1">{{ $planExercise->exercise->description }}</p>
                    <div class="mt-3 flex gap-4 text-sm text-gray-600">
                        <span>📅 {{ $planExercise->day_of_week }}</span>
                        <span>🕐 {{ substr($planExercise->scheduled_time, 0, 5) }}</span>
                        <span>⏱️ {{ $planExercise->custom_duration_minutes ?? $planExercise->exercise->duration_minutes }} min</span>
                        <span>🔄 {{ $planExercise->custom_repetitions ?? $planExercise->exercise->repetitions }} reps</span>
                    </div>
                </div>
                <button class="text-blue-600 hover:text-blue-800 font-medium">Complete</button>
            </div>
        </div>
        @empty
        <div class="px-6 py-4 text-center text-gray-600">
            No exercises scheduled for the next 24 hours. Check back later!
        </div>
        @endforelse
    </div>
</div>
@else
<div class="bg-white rounded-lg shadow p-8 text-center">
    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Active Plan</h3>
    <p class="text-gray-600">Your clinician hasn't created a rehabilitation plan yet. Please contact them to get started.</p>
</div>
@endif
@endsection