@extends('layouts.patient')

@section('title', 'Submit Feedback')
@section('page_title', 'Submit Plan Feedback')

@section('content')

{{-- Eligibility Status Banner --}}
@if($eligible)
<div class="mb-6 bg-green-50 border border-green-200 rounded-xl px-6 py-4 flex items-start gap-4">
    <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <div>
        <p class="font-semibold text-green-800">You are eligible to submit feedback!</p>
        <p class="text-green-700 text-sm mt-1">
            Plan age: <strong>{{ $daysOnPlan }} days</strong> &nbsp;·&nbsp;
            Completion rate: <strong>{{ $completionRate }}%</strong>
        </p>
    </div>
</div>
@else
<div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl px-6 py-4 flex items-start gap-4">
    <svg class="w-6 h-6 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
    </svg>
    <div>
        <p class="font-semibold text-amber-800">You are not yet eligible to submit feedback.</p>
        <ul class="mt-2 space-y-1">
            @foreach($reasons as $reason)
            <li class="text-amber-700 text-sm flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 flex-shrink-0"></span>
                {{ $reason }}
            </li>
            @endforeach
        </ul>
        @if($activePlan)
        <div class="mt-3 flex gap-6 text-xs text-amber-700">
            <span>Plan age: <strong>{{ $daysOnPlan }}/30 days</strong></span>
            <span>Completion: <strong>{{ $completionRate }}%</strong> (need ≥60%)</span>
        </div>
        @endif
    </div>
</div>
@endif

@if($eligible && $activePlan)
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="bg-gradient-to-r from-green-500 to-teal-500 px-6 py-5">
        <h2 class="text-xl font-bold text-white">{{ $activePlan->plan_name }}</h2>
        <p class="text-green-100 text-sm mt-1">Rate each exercise so your clinician can refine your next plan.</p>
    </div>

    <form method="POST" action="{{ route('patient.plan-feedback', $activePlan->id) }}" class="p-6">
        @csrf

        @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="space-y-6">
            @foreach($planExercises->unique('exercise_id') as $planExercise)
            @php $exIdx = $loop->index; @endphp
            <div class="border border-gray-200 rounded-lg p-5">
                <input type="hidden" name="exercises[{{ $exIdx }}][plan_exercise_id]" value="{{ $planExercise->id }}">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-semibold text-gray-900 text-base">{{ $planExercise->exercise->name }}</h4>
                    <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">{{ $planExercise->day_of_week }}</span>
                </div>

                <div class="space-y-4">
                    {{-- Pain Level --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pain Level <span class="text-gray-400 font-normal">(0 = none, 10 = severe)</span>
                        </label>
                        <div class="flex flex-wrap gap-1.5">
                            @for($i = 0; $i <= 10; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" name="exercises[{{ $exIdx }}][pain_level]" value="{{ $i }}" class="sr-only peer" @if($i===0) checked @endif>
                                <span class="w-8 h-8 flex items-center justify-center rounded-full text-xs font-semibold border border-gray-300 peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500 hover:bg-red-100 transition-colors select-none">{{ $i }}</span>
                            </label>
                            @endfor
                        </div>
                    </div>

                    {{-- Difficulty --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Difficulty <span class="text-gray-400 font-normal">(1 = easy, 5 = very hard)</span>
                        </label>
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

        {{-- Overall Comments --}}
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Overall Comments <span class="text-gray-400 font-normal">(optional)</span></label>
            <textarea name="overall_comments" rows="4"
                placeholder="Any other feedback for your clinician — e.g. pain location, things that were too easy or too hard..."
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none text-sm">{{ old('overall_comments') }}</textarea>
        </div>

        <div class="mt-6 flex items-center gap-4">
            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Submit Feedback
            </button>
            <a href="{{ route('patient.dashboard') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium">
                Cancel
            </a>
        </div>
    </form>
</div>

@elseif($activePlan)
{{-- Not eligible but has a plan — show progress toward eligibility --}}
<div class="bg-white rounded-xl shadow p-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-6">Your progress toward eligibility</h3>
    <div class="space-y-5">
        <div>
            <div class="flex justify-between text-sm mb-1">
                <span class="text-gray-600">Plan age</span>
                <span class="font-semibold {{ $daysOnPlan >= 30 ? 'text-green-600' : 'text-gray-700' }}">{{ $daysOnPlan }} / 30 days</span>
            </div>
            <div class="bg-gray-200 rounded-full h-3">
                <div class="h-3 rounded-full {{ $daysOnPlan >= 30 ? 'bg-green-500' : 'bg-blue-400' }} transition-all"
                    style="width: {{ min(100, round(($daysOnPlan / 30) * 100)) }}%"></div>
            </div>
        </div>
        <div>
            <div class="flex justify-between text-sm mb-1">
                <span class="text-gray-600">Exercise completion</span>
                <span class="font-semibold {{ $completionRate >= 60 ? 'text-green-600' : 'text-gray-700' }}">{{ $completionRate }}% / 60%</span>
            </div>
            <div class="bg-gray-200 rounded-full h-3">
                <div class="h-3 rounded-full {{ $completionRate >= 60 ? 'bg-green-500' : 'bg-blue-400' }} transition-all"
                    style="width: {{ min(100, $completionRate) }}%"></div>
            </div>
        </div>
    </div>
    <p class="text-gray-500 text-sm mt-6">Keep completing your exercises and check back once both criteria are met.</p>
    <a href="{{ route('patient.schedule') }}" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-medium text-sm">
        Go to My Schedule
    </a>
</div>

@else
<div class="bg-white rounded-xl shadow p-12 text-center">
    <svg class="w-14 h-14 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
    <h3 class="text-lg font-semibold text-gray-700">No Active Plan</h3>
    <p class="text-gray-500 text-sm mt-2">You need an active rehabilitation plan before submitting feedback.</p>
</div>
@endif

@endsection
