@extends('layouts.clinician')

@section('page_title', '')

@section('content')
<div class="max-w-7xl mx-auto">

    @if($feedbackByPatient->isEmpty())
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
        </svg>
        <h3 class="text-lg font-semibold text-gray-700 mb-1">No Feedback Yet</h3>
        <p class="text-gray-500 text-sm">Feedback will appear here once patients have completed one month of their rehabilitation plan.</p>
    </div>
    @else
    <div class="space-y-8">
        @foreach($feedbackByPatient as $patientId => $feedbackItems)
        @php
        $patient = $feedbackItems->first()->patient;
        $feedbackByPlan = $feedbackItems->groupBy('rehab_plan_id');
        @endphp

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Patient Header -->
            <div class="bg-gradient-to-r from-sky-500 to-blue-600 px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">{{ $patient->user->name }}</h2>
                        <p class="text-sky-200 text-sm">{{ $feedbackItems->count() }} exercise rating(s) across {{ $feedbackByPlan->count() }} plan(s)</p>
                    </div>
                </div>
                <a href="{{ route('clinician.patients.show', $patient->id) }}"
                    class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    View Patient
                </a>
            </div>

            <!-- Plans with Feedback -->
            <div class="divide-y divide-gray-100">
                @foreach($feedbackByPlan as $planId => $planFeedbackItems)
                @php
                $plan = $planFeedbackItems->first()->rehabPlan;
                $hasNewerPlan = $plan && $patient->rehabPlans()
                ->where('created_at', '>', $plan->created_at)->exists();

                // Group by submission session: round feedback_date down to the nearest minute
                $bySession = $planFeedbackItems->groupBy(function($fb) {
                return $fb->feedback_date
                ? \Carbon\Carbon::parse($fb->feedback_date)->format('Y-m-d H:i')
                : 'unknown';
                })->sortKeysDesc(); // newest first
                @endphp

                <div class="px-6 py-5">
                    <!-- Plan Header -->
                    <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
                        <div>
                            <h3 class="font-semibold text-gray-900 text-base">
                                {{ $plan ? $plan->plan_name : 'Plan #'.$planId }}
                            </h3>
                            @if($plan)
                            <p class="text-gray-500 text-xs mt-0.5">Plan started {{ $plan->start_date->format('M d, Y') }} · {{ $bySession->count() }} submission(s)</p>
                            @endif
                        </div>
                        @if(!$hasNewerPlan)
                        <a href="{{ route('clinician.plans.create', $patient->id) }}?from_feedback={{ $planId }}"
                            class="inline-flex items-center gap-2 bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 font-medium text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Generate New Plan
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

                    <!-- Submission Sessions (newest first) -->
                    <div class="space-y-4">
                        @foreach($bySession as $sessionKey => $sessionItems)
                        @php
                        $sessionDate = $sessionKey !== 'unknown'
                        ? \Carbon\Carbon::parse($sessionKey)->format('M d, Y \a\t H:i')
                        : 'Unknown time';
                        $sessionAvgPain = round($sessionItems->avg('pain_level'), 1);
                        $sessionAvgDiff = round($sessionItems->avg('difficulty_rating'), 1);
                        $sessionComment = $sessionItems->first()->overall_comments;
                        $painColor = $sessionAvgPain >= 7 ? 'bg-red-100 text-red-800' : ($sessionAvgPain >= 4 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                        $diffColor = $sessionAvgDiff >= 4 ? 'bg-red-100 text-red-800' : ($sessionAvgDiff >= 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                        $sessionIdx = $loop->index;
                        @endphp

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <!-- Session Header (clickable toggle) -->
                            <div class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
                                <button type="button"
                                    onclick="toggleSession('session-{{ $patientId }}-{{ $planId }}-{{ $sessionIdx }}')"
                                    class="flex-1 flex items-center gap-3 text-left">
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold text-gray-800">Submitted {{ $sessionDate }}</span>
                                            <span class="text-xs text-gray-500">{{ $sessionItems->count() }} exercise(s)</span>
                                        </div>
                                        <span class="text-xs text-sky-600 font-medium">{{ $patient->user->name }}</span>
                                    </div>
                                </button>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $painColor }}">Pain {{ $sessionAvgPain }}/10</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $diffColor }}">Diff {{ $sessionAvgDiff }}/5</span>
                                    <button type="button"
                                        onclick="confirmDeleteSession('{{ $patientId }}', '{{ $planId }}', '{{ $sessionKey }}')"
                                        class="ml-1 p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 hover:text-red-700 transition"
                                        title="Delete this submission">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    <svg class="w-4 h-4 text-gray-400 session-chevron-{{ $patientId }}-{{ $planId }}-{{ $sessionIdx }} transition-transform cursor-pointer"
                                        onclick="toggleSession('session-{{ $patientId }}-{{ $planId }}-{{ $sessionIdx }}')"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Session Body -->
                            <div id="session-{{ $patientId }}-{{ $planId }}-{{ $sessionIdx }}" class="{{ $sessionIdx === 0 ? '' : 'hidden' }} px-4 py-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-3">
                                    @foreach($sessionItems as $fb)
                                    @if($fb->planExercise && $fb->planExercise->exercise)
                                    @php
                                    $exPainColor = ($fb->pain_level ?? 0) >= 7 ? 'bg-red-500' : (($fb->pain_level ?? 0) >= 4 ? 'bg-yellow-500' : 'bg-green-500');
                                    $exDiffColor = ($fb->difficulty_rating ?? 0) >= 4 ? 'bg-red-500' : (($fb->difficulty_rating ?? 0) >= 3 ? 'bg-yellow-500' : 'bg-green-500');
                                    @endphp
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                        <p class="font-medium text-gray-900 text-sm mb-2">{{ $fb->planExercise->exercise->name }}</p>
                                        <div class="space-y-1">
                                            <div class="flex items-center justify-between text-xs text-gray-600">
                                                <span>Pain</span>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-2 h-2 rounded-full {{ $exPainColor }}"></span>
                                                    <span class="font-semibold">{{ $fb->pain_level ?? 'N/A' }}/10</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between text-xs text-gray-600">
                                                <span>Difficulty</span>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-2 h-2 rounded-full {{ $exDiffColor }}"></span>
                                                    <span class="font-semibold">{{ $fb->difficulty_rating ?? 'N/A' }}/5</span>
                                                </div>
                                            </div>
                                            <div class="mt-1.5">
                                                <div class="bg-gray-200 rounded-full h-1.5">
                                                    <div class="h-1.5 rounded-full {{ ($fb->pain_level ?? 0) >= 7 ? 'bg-red-500' : (($fb->pain_level ?? 0) >= 4 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                                        style="width: {{ (($fb->pain_level ?? 0) / 10) * 100 }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                                @if($sessionComment)
                                <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                                    <p class="text-sm text-amber-900">
                                        <span class="font-medium">Comment:</span> "{{ $sessionComment }}"
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <script>
                function toggleSession(id) {
                    const el = document.getElementById(id);
                    if (!el) return;
                    el.classList.toggle('hidden');
                    const header = el.previousElementSibling;
                    const chevron = header ? header.querySelector('.transition-transform') : null;
                    if (chevron) chevron.classList.toggle('rotate-180');
                }

                const deleteFeedbackUrl = "{{ route('clinician.feedback.delete-session') }}";

                function confirmDeleteSession(patientId, planId, sessionKey) {
                    if (!confirm('Delete all feedback from this submission? This cannot be undone.')) return;
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = deleteFeedbackUrl;
                    form.innerHTML = `
                        <input name="_token" value="{{ csrf_token() }}">
                        <input name="_method" value="DELETE">
                        <input name="patient_id" value="${patientId}">
                        <input name="plan_id" value="${planId}">
                        <input name="session_key" value="${sessionKey}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            </script>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection