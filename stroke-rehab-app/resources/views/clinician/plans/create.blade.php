@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('clinician.patients.show', $patient->id) }}" class="text-blue-600 hover:text-blue-800 font-medium mb-4 inline-block">← Back</a>
            <h1 class="text-4xl font-bold text-gray-900">Create Rehabilitation Plan</h1>
            <p class="text-gray-600 mt-2">For {{ $patient->user->name }}</p>
        </div>

        @if(!empty($feedbackSuggestion))
        <div class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-amber-500 p-5 rounded-lg">
            <h3 class="text-base font-bold text-amber-900 mb-2">📋 Plan Based on Patient Feedback</h3>
            <p class="text-sm text-amber-800 mb-3">
                The patient's previous feedback (avg pain: <strong>{{ $feedbackSuggestion['avg_pain'] }}/10</strong>,
                avg difficulty: <strong>{{ $feedbackSuggestion['avg_difficulty'] }}/5</strong>) suggests
                <strong>{{ $feedbackSuggestion['reason'] }}</strong>.
            </p>
            @if($feedbackSuggestion['overall_comments'])
            <p class="text-sm text-amber-700 mb-3 italic">"{{ $feedbackSuggestion['overall_comments'] }}"</p>
            @endif
            <p class="text-sm font-medium text-amber-900">
                Recommended difficulty for next plan:
                <span class="px-2 py-1 bg-amber-200 text-amber-900 rounded font-bold">Level {{ $feedbackSuggestion['suggested_difficulty'] }}/5</span>
            </p>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow p-8">
            @if($mlAvailable && $mlPrediction)
            <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-6 rounded-lg">
                <h2 class="text-lg font-bold text-blue-900 mb-6">🤖 AI-Powered ML Recommendations</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white p-4 rounded border border-blue-200">
                        <p class="text-blue-700 text-sm font-medium">Recovery Probability (6-month)</p>
                        <p class="text-3xl font-bold text-blue-900">{{ round($mlPrediction['recovery_probability'] * 100, 1) }}%</p>
                        <p class="text-xs text-blue-600 mt-2">
                            @if($mlPrediction['recovery_probability'] > 0.8)
                            Excellent recovery potential
                            @elseif($mlPrediction['recovery_probability'] > 0.6)
                            Good recovery potential
                            @elseif($mlPrediction['recovery_probability'] > 0.4)
                            Moderate recovery potential
                            @else
                            Limited recovery potential - Conservative approach recommended
                            @endif
                        </p>
                    </div>

                    <div class="bg-white p-4 rounded border border-indigo-200">
                        <p class="text-indigo-700 text-sm font-medium">Model Confidence</p>
                        <p class="text-3xl font-bold text-indigo-900">{{ round($mlPrediction['confidence_score'] * 100, 1) }}%</p>
                        <p class="text-xs text-indigo-600 mt-2">Prediction reliability score</p>
                    </div>
                </div>

                <div class="bg-white p-4 rounded border border-blue-200 mb-6">
                    <p class="text-blue-700 text-sm font-medium mb-3">Recommended Difficulty Level</p>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-bold text-blue-900">Level {{ $mlPrediction['difficulty_level'] }}/5</span>
                        <span class="text-sm text-blue-600">
                            @if($mlPrediction['difficulty_level'] == 1) (Very Easy - Conservative)
                            @elseif($mlPrediction['difficulty_level'] == 2) (Easy)
                            @elseif($mlPrediction['difficulty_level'] == 3) (Moderate)
                            @elseif($mlPrediction['difficulty_level'] == 4) (Hard)
                            @else (Very Hard - Intensive)
                            @endif
                        </span>
                        @if(!empty($feedbackSuggestion))
                        <span class="ml-2 px-2 py-0.5 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full">Adjusted from patient feedback</span>
                        @endif
                    </div>
                </div>

                <div class="bg-white p-4 rounded border border-green-200 mb-6">
                    <p class="text-green-700 text-sm font-medium mb-3">Recommended Exercises</p>
                    <div class="space-y-2">
                        @foreach($mlPrediction['recommended_exercises'] as $exercise)
                        <div class="flex items-start gap-3">
                            <span class="text-green-600 font-bold">✓</span>
                            <div>
                                <p class="font-medium text-gray-900">{{ $exercise['name'] }}</p>
                                <p class="text-sm text-gray-600">{{ $exercise['target_deficit'] }} • {{ $exercise['body_region'] }} • Difficulty: {{ $exercise['difficulty'] }}/5</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $exercise['instructions'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-blue-100 p-4 rounded border border-blue-300">
                    <p class="text-sm text-blue-900"><strong>Clinical Notes:</strong> {{ $mlPrediction['clinical_notes'] }}</p>
                </div>

                <p class="text-sm text-blue-700 mt-4">💡 <strong>Tip:</strong>
                    @if(!empty($feedbackSuggestion))
                    Patient feedback adjusted the difficulty to Level {{ $mlPrediction['difficulty_level'] }}. Exercises shown are filtered to this level. You may further adjust using the form below.
                    @else
                    The AI recommends difficulty level {{ $mlPrediction['difficulty_level'] }}. You can adjust this based on your clinical judgment and patient response.
                    @endif
                </p>
            </div>
            @elseif($mlError)
            <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-6 rounded">
                <p class="text-red-800 text-sm"><strong>ML Service Error:</strong> {{ $mlError }}</p>
                <p class="text-red-700 text-sm mt-2">You can still create a plan manually below.</p>
            </div>
            @elseif(!$mlAvailable)
            <div class="mb-8 bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded">
                <p class="text-yellow-800 text-sm">⚠️ <strong>ML Service Unavailable:</strong> The FastAPI ML service is not running. Start it to enable AI-powered recommendations.</p>
                <p class="text-yellow-700 text-sm mt-2">You can still create a plan manually using the form below.</p>
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

                <div>
                    <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-2">Difficulty Level *</label>
                    <select id="difficulty_level" name="difficulty_level" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('difficulty_level') border-red-500 @enderror">
                        <option value="">Select difficulty level</option>
                        @php $suggestedLevel = !empty($feedbackSuggestion) ? $feedbackSuggestion['suggested_difficulty'] : ($mlPrediction['difficulty_level'] ?? null); @endphp
                        <option value="1" @if($suggestedLevel==1) selected @endif>Level 1 - Very Easy</option>
                        <option value="2" @if($suggestedLevel==2) selected @endif>Level 2 - Easy</option>
                        <option value="3" @if($suggestedLevel==3) selected @endif>Level 3 - Moderate</option>
                        <option value="4" @if($suggestedLevel==4) selected @endif>Level 4 - Hard</option>
                        <option value="5" @if($suggestedLevel==5) selected @endif>Level 5 - Very Hard</option>
                    </select>
                    @error('difficulty_level')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if($mlPrediction)
                <input type="hidden" name="recovery_probability" value="{{ $mlPrediction['recovery_probability'] }}">
                <input type="hidden" name="ml_confidence_score" value="{{ $mlPrediction['confidence_score'] }}">
                @endif

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