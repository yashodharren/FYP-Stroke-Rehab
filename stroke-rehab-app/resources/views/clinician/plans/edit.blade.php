@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('clinician.patients.show', $plan->patient_id) }}" class="text-blue-600 hover:text-blue-800 font-medium mb-4 inline-block">← Back</a>
            <h1 class="text-4xl font-bold text-gray-900">Edit Rehabilitation Plan</h1>
            <p class="text-gray-600 mt-2">{{ $plan->plan_name }}</p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Add Exercises to Plan</h2>
                    <form method="POST" action="{{ route('clinician.plans.add-exercise', $plan->id) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="exercise_id" class="block text-sm font-medium text-gray-700 mb-2">Select Exercise *</label>
                            <select id="exercise_id" name="exercise_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Choose an exercise...</option>
                                @foreach($exercises as $exercise)
                                    <option value="{{ $exercise->id }}">{{ $exercise->name }} (Level {{ $exercise->difficulty_level }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="day_of_week" class="block text-sm font-medium text-gray-700 mb-2">Day of Week *</label>
                                <select id="day_of_week" name="day_of_week" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select day</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                    <option value="Sunday">Sunday</option>
                                </select>
                            </div>

                            <div>
                                <label for="frequency_per_week" class="block text-sm font-medium text-gray-700 mb-2">Frequency per Week *</label>
                                <input type="number" id="frequency_per_week" name="frequency_per_week" min="1" max="7" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="1">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="scheduled_time" class="block text-sm font-medium text-gray-700 mb-2">Scheduled Time</label>
                                <input type="time" id="scheduled_time" name="scheduled_time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="custom_repetitions" class="block text-sm font-medium text-gray-700 mb-2">Custom Repetitions</label>
                                <input type="number" id="custom_repetitions" name="custom_repetitions" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Leave blank to use default">
                            </div>
                        </div>

                        <div>
                            <label for="custom_duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Custom Duration (minutes)</label>
                            <input type="number" id="custom_duration_minutes" name="custom_duration_minutes" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Leave blank to use default">
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">
                            Add Exercise to Plan
                        </button>
                    </form>
                </div>
            </div>

            <div>
                <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Plan Summary</h2>
                    <div class="space-y-3 mb-6">
                        <div>
                            <p class="text-gray-600 text-sm">Status</p>
                            <p class="font-semibold text-gray-900">{{ ucfirst($plan->status) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Difficulty Level</p>
                            <p class="font-semibold text-gray-900">{{ $plan->difficulty_level }}/5</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Total Exercises</p>
                            <p class="font-semibold text-gray-900">{{ $planExercises->count() }}</p>
                        </div>
                    </div>

                    @if($plan->status === 'draft' && $planExercises->count() > 0)
                        <form method="POST" action="{{ route('clinician.plans.publish', $plan->id) }}">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-medium">
                                Publish Plan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Exercises in Plan</h2>
            <div class="divide-y divide-gray-200">
                @forelse($planExercises as $planExercise)
                    <div class="py-4 first:pt-0 last:pb-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $planExercise->exercise->name }}</h3>
                                <p class="text-gray-600 text-sm mt-1">{{ $planExercise->exercise->description }}</p>
                                <div class="mt-2 flex gap-4 text-sm text-gray-600">
                                    <span>📅 {{ $planExercise->day_of_week }}</span>
                                    <span>🔄 {{ $planExercise->frequency_per_week }}x/week</span>
                                    <span>⏱️ {{ $planExercise->custom_duration_minutes ?? $planExercise->exercise->duration_minutes }} min</span>
                                    <span>💪 {{ $planExercise->custom_repetitions ?? $planExercise->exercise->repetitions }} reps</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600 py-4">No exercises added yet. Add exercises using the form above.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
