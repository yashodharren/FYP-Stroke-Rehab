@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-teal-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('patient.dashboard') }}" class="text-green-600 hover:text-green-800 font-medium mb-4 inline-block">← Back to Dashboard</a>
            <h1 class="text-4xl font-bold text-gray-900">Weekly Rehabilitation Schedule</h1>
            <p class="text-gray-600 mt-2">{{ $activePlan->plan_name ?? 'Your Rehabilitation Plan' }}</p>
        </div>

        @if($activePlan)
            <div class="grid grid-cols-1 gap-6">
                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-teal-500 px-6 py-4">
                            <h2 class="text-2xl font-bold text-white">{{ $day }}</h2>
                        </div>
                        <div class="p-6">
                            @if($schedule[$day]->count() > 0)
                                <div class="space-y-4">
                                    @foreach($schedule[$day] as $planExercise)
                                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                            <div class="flex justify-between items-start mb-3">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900">{{ $planExercise->exercise->name }}</h3>
                                                    <p class="text-gray-600 text-sm mt-1">{{ $planExercise->exercise->description }}</p>
                                                </div>
                                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $planExercise->frequency_per_week }}x/week
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-sm">
                                                <div>
                                                    <p class="text-gray-600">Duration</p>
                                                    <p class="font-semibold text-gray-900">{{ $planExercise->custom_duration_minutes ?? $planExercise->exercise->duration_minutes }} min</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-600">Repetitions</p>
                                                    <p class="font-semibold text-gray-900">{{ $planExercise->custom_repetitions ?? $planExercise->exercise->repetitions }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-600">Target Area</p>
                                                    <p class="font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $planExercise->exercise->target_area)) }}</p>
                                                </div>
                                                @if($planExercise->scheduled_time)
                                                    <div>
                                                        <p class="text-gray-600">Scheduled Time</p>
                                                        <p class="font-semibold text-gray-900">{{ $planExercise->scheduled_time->format('H:i') }}</p>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="bg-gray-50 p-3 rounded mb-4">
                                                <p class="text-sm text-gray-700"><strong>Instructions:</strong> {{ $planExercise->exercise->instructions }}</p>
                                            </div>

                                            <form method="POST" action="{{ route('patient.feedback', $planExercise->id) }}" class="space-y-4 border-t border-gray-200 pt-4">
                                                @csrf

                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <div>
                                                        <label for="pain_level_{{ $planExercise->id }}" class="block text-sm font-medium text-gray-700 mb-2">Pain Level (0-10)</label>
                                                        <input type="number" id="pain_level_{{ $planExercise->id }}" name="pain_level" min="0" max="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                                    </div>

                                                    <div>
                                                        <label for="difficulty_rating_{{ $planExercise->id }}" class="block text-sm font-medium text-gray-700 mb-2">Difficulty (1-5)</label>
                                                        <select id="difficulty_rating_{{ $planExercise->id }}" name="difficulty_rating" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                                            <option value="">Select difficulty</option>
                                                            <option value="1">Very Easy</option>
                                                            <option value="2">Easy</option>
                                                            <option value="3">Moderate</option>
                                                            <option value="4">Hard</option>
                                                            <option value="5">Very Hard</option>
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label for="mood_rating_{{ $planExercise->id }}" class="block text-sm font-medium text-gray-700 mb-2">Mood (1-5)</label>
                                                        <select id="mood_rating_{{ $planExercise->id }}" name="mood_rating" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                                            <option value="">Select mood</option>
                                                            <option value="1">Very Bad</option>
                                                            <option value="2">Bad</option>
                                                            <option value="3">Neutral</option>
                                                            <option value="4">Good</option>
                                                            <option value="5">Very Good</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label for="comments_{{ $planExercise->id }}" class="block text-sm font-medium text-gray-700 mb-2">Comments</label>
                                                    <textarea id="comments_{{ $planExercise->id }}" name="comments" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="How did the exercise feel?"></textarea>
                                                </div>

                                                <div class="flex gap-4">
                                                    <label class="flex items-center gap-2 cursor-pointer">
                                                        <input type="checkbox" name="completed_exercise" value="1" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-2 focus:ring-green-500">
                                                        <span class="text-sm font-medium text-gray-700">I completed this exercise</span>
                                                    </label>
                                                    <button type="submit" class="ml-auto bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-medium text-sm">
                                                        Submit Feedback
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-600 text-center py-8">No exercises scheduled for {{ $day }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-600">No active rehabilitation plan. Please contact your clinician.</p>
            </div>
        @endif
    </div>
</div>
@endsection
