@extends('layouts.patient')

@section('title', 'Patient Dashboard')
@section('page_title', 'My Rehabilitation Plan')

@section('content')

@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
    {{ session('success') }}
</div>
@endif

@if($activePlan)
@php
$totalExercises = $planExercises->count();
$completedExercises = $planExercises->where('is_completed', true)->count();
$completionPct = $totalExercises > 0 ? round(($completedExercises / $totalExercises) * 100) : 0;
@endphp

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
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
        <p class="text-2xl font-bold text-gray-900">{{ $totalExercises }}</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-medium">Completion</p>
        <p class="text-2xl font-bold {{ $completionPct >= 60 ? 'text-green-600' : 'text-gray-900' }}">{{ $completedExercises }}/{{ $totalExercises }}</p>
        <div class="mt-2 bg-gray-200 rounded-full h-2">
            <div class="bg-green-500 h-2 rounded-full transition-all" style="width: {{ $completionPct }}%"></div>
        </div>
        <p class="text-xs text-gray-500 mt-1">{{ $completionPct }}% done</p>
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

@php
$todayName = \Carbon\Carbon::now()->format('l');
@endphp
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">Today's Exercises — {{ \Carbon\Carbon::now()->format('l, M d') }}</h2>
        <p class="text-gray-500 text-sm mt-1">Only today's exercises can be marked as done.</p>
    </div>
    <div class="divide-y divide-gray-200">
        @forelse($upcomingExercises as $planExercise)
        @php $isToday = $planExercise->day_of_week === $todayName; @endphp
        <div class="px-6 py-4 hover:bg-gray-50 {{ $planExercise->is_completed ? 'bg-green-50' : '' }}">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold {{ $planExercise->is_completed ? 'line-through text-gray-400' : 'text-gray-900' }}">{{ $planExercise->exercise->name }}</h3>
                    <p class="text-gray-600 text-sm mt-1">{{ $planExercise->exercise->description }}</p>
                    <div class="mt-3 flex gap-4 text-sm text-gray-600">
                        <span>📅 {{ $planExercise->day_of_week }}</span>
                        <span>🕐 {{ substr($planExercise->scheduled_time, 0, 5) }}</span>
                        <span>⏱️ {{ $planExercise->custom_duration_minutes ?? $planExercise->exercise->duration_minutes }} min</span>
                        <span>🔄 {{ $planExercise->custom_repetitions ?? $planExercise->exercise->repetitions }} reps</span>
                    </div>
                </div>
                @if($isToday)
                <form method="POST" action="{{ route('patient.mark-done', $planExercise->id) }}">
                    @csrf
                    <button type="submit"
                        class="{{ $planExercise->is_completed ? 'bg-gray-200 text-gray-600 hover:bg-gray-300' : 'bg-green-600 text-white hover:bg-green-700' }} px-4 py-2 rounded-lg font-medium text-sm">
                        {{ $planExercise->is_completed ? 'Undo' : 'Mark Done' }}
                    </button>
                </form>
                @elseif($planExercise->is_completed)
                <span class="px-4 py-2 bg-green-100 text-green-700 rounded-lg font-medium text-sm">✓ Done</span>
                @else
                <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg font-medium text-sm cursor-not-allowed" title="Can only mark today's exercises as done">Locked</span>
                @endif
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

@if(!empty($showFeedbackPrompt) && $activePlan)
<!-- Feedback Modal -->
<div id="feedbackModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-screen overflow-y-auto">
        <div class="bg-gradient-to-r from-green-500 to-teal-500 px-6 py-5 rounded-t-2xl">
            <h2 class="text-2xl font-bold text-white">Time for a Check-in! 🎉</h2>
            <p class="text-green-100 text-sm mt-1">You've been on your plan for over a month — great work! Please rate each exercise so your clinician can refine your plan.</p>
        </div>
        <form method="POST" action="{{ route('patient.plan-feedback', $activePlan->id) }}" class="p-6">
            @csrf
            <div class="space-y-6">
                @foreach($planExercises->unique('exercise_id') as $planExercise)
                @php $exIdx = $loop->index; @endphp
                <div class="border border-gray-200 rounded-lg p-4">
                    <input type="hidden" name="exercises[{{ $exIdx }}][plan_exercise_id]" value="{{ $planExercise->id }}">
                    <h4 class="font-semibold text-gray-900 mb-3">{{ $planExercise->exercise->name }}</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pain Level <span class="text-gray-400 font-normal">(0 = none, 10 = severe)</span></label>
                            <div class="flex flex-wrap gap-1.5">
                                @for($i = 0; $i <= 10; $i++)
                                    <label class="cursor-pointer">
                                    <input type="radio" name="exercises[{{ $exIdx }}][pain_level]" value="{{ $i }}" class="sr-only peer" @if($i===0) checked @endif>
                                    <span class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-semibold border border-gray-300 peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500 hover:bg-red-100 transition-colors select-none">{{ $i }}</span>
                                    </label>
                                    @endfor
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty <span class="text-gray-400 font-normal">(1 = easy, 5 = very hard)</span></label>
                            <div class="flex gap-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <label class="cursor-pointer">
                                    <input type="radio" name="exercises[{{ $exIdx }}][difficulty_rating]" value="{{ $i }}" class="sr-only peer" @if($i===3) checked @endif>
                                    <span class="w-10 h-10 flex items-center justify-center rounded-full text-sm font-semibold border border-gray-300 peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-blue-500 hover:bg-blue-100 transition-colors select-none">{{ $i }}</span>
                                    </label>
                                    @endfor
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Overall Comments (optional)</label>
                <textarea name="overall_comments" rows="3" placeholder="Any other feedback for your clinician..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"></textarea>
            </div>
            <div class="mt-6 flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('feedbackModal').classList.add('hidden')"
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                    Remind Me Later
                </button>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    Submit Feedback
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection