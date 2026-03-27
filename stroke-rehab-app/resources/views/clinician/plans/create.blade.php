@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('clinician.patients.show', $patient->id) }}" class="text-blue-600 hover:text-blue-800 font-medium mb-4 inline-block">← Back</a>
            <h1 class="text-4xl font-bold text-gray-900">Create Rehabilitation Plan</h1>
            <p class="text-gray-600 mt-2">For {{ $patient->user->name }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-8">
            @if($mlAvailable && $mlPrediction)
            <div class="mb-8 bg-blue-50 border-l-4 border-blue-500 p-6 rounded">
                <h2 class="text-lg font-bold text-blue-900 mb-4">🤖 AI-Powered Recommendations</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-blue-700 text-sm font-medium">Recovery Probability</p>
                        <p class="text-2xl font-bold text-blue-900">{{ ($mlPrediction['recovery_probability'] * 100) }}%</p>
                    </div>
                    <div>
                        <p class="text-blue-700 text-sm font-medium">Recommended Difficulty</p>
                        <p class="text-2xl font-bold text-blue-900">Level {{ $mlPrediction['difficulty_level'] }}/5</p>
                    </div>
                    <div>
                        <p class="text-blue-700 text-sm font-medium">Confidence Score</p>
                        <p class="text-2xl font-bold text-blue-900">{{ ($mlPrediction['confidence_score'] * 100) }}%</p>
                    </div>
                    <div>
                        <p class="text-blue-700 text-sm font-medium">Suggested Exercises</p>
                        <p class="text-sm text-blue-900 mt-1">{{ implode(', ', array_slice($mlPrediction['recommended_exercises'], 0, 2)) }}</p>
                    </div>
                </div>
                <p class="text-sm text-blue-700 mt-4">💡 Tip: The AI suggests a difficulty level {{ $mlPrediction['difficulty_level'] }} plan. You can adjust this based on your clinical judgment.</p>
            </div>
            @elseif(!$mlAvailable)
            <div class="mb-8 bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded">
                <p class="text-yellow-800 text-sm">⚠️ ML Service is not available. Using manual plan creation. Start the FastAPI service to enable AI recommendations.</p>
            </div>
            @endif

            <form method="POST" action="{{ route('clinician.plans.store', $patient->id) }}" class="space-y-6">
                @csrf

                <div>
                    <label for="plan_name" class="block text-sm font-medium text-gray-700 mb-2">Plan Name *</label>
                    <input type="text" id="plan_name" name="plan_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('plan_name') border-red-500 @enderror" placeholder="e.g., Post-Stroke Recovery Phase 1">
                    @error('plan_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror" placeholder="Describe the rehabilitation plan goals and approach..."></textarea>
                    @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-2">Difficulty Level *</label>
                        <select id="difficulty_level" name="difficulty_level" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('difficulty_level') border-red-500 @enderror">
                            <option value="">Select difficulty level</option>
                            <option value="1">Level 1 - Very Easy</option>
                            <option value="2">Level 2 - Easy</option>
                            <option value="3">Level 3 - Moderate</option>
                            <option value="4">Level 4 - Hard</option>
                            <option value="5">Level 5 - Very Hard</option>
                        </select>
                        @error('difficulty_level')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="recovery_probability" class="block text-sm font-medium text-gray-700 mb-2">Recovery Probability (0-1)</label>
                        <input type="number" id="recovery_probability" name="recovery_probability" min="0" max="1" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('recovery_probability') border-red-500 @enderror" placeholder="0.75">
                        @error('recovery_probability')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                        <input type="date" id="start_date" name="start_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('end_date') border-red-500 @enderror">
                        @error('end_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
                        Create Plan
                    </button>
                    <a href="{{ route('clinician.patients.show', $patient->id) }}" class="flex-1 bg-gray-300 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-400 font-medium text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection