@extends('layouts.clinician')

@section('page_title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="text-gray-600 mt-2">Here's your rehabilitation management overview</p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="bg-blue-100 rounded-full p-3 flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Patients</p>
                <p class="text-2xl font-bold text-gray-900">{{ $patients->count() }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="bg-green-100 rounded-full p-3 flex-shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Active Plans</p>
                <p class="text-2xl font-bold text-gray-900">{{ $activePlans }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="bg-purple-100 rounded-full p-3 flex-shrink-0">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Completed Plans</p>
                <p class="text-2xl font-bold text-gray-900">{{ $completedPlans }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="bg-amber-100 rounded-full p-3 flex-shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Draft Plans</p>
                <p class="text-2xl font-bold text-gray-900">{{ $draftPlans }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Weekly Adherence --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-base font-bold text-gray-800 mb-1">Weekly Exercise Adherence</h2>
            <p class="text-xs text-gray-500 mb-4">Across all active plans</p>
            <div class="flex items-end gap-3 mb-3">
                <span class="text-4xl font-bold {{ $adherencePct >= 70 ? 'text-green-600' : ($adherencePct >= 40 ? 'text-amber-500' : 'text-red-500') }}">{{ $adherencePct }}%</span>
                <span class="text-sm text-gray-500 mb-1">{{ $completedWeek }}/{{ $totalWeek }} exercises</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                <div class="h-3 rounded-full {{ $adherencePct >= 70 ? 'bg-green-500' : ($adherencePct >= 40 ? 'bg-amber-400' : 'bg-red-400') }}" style="width: {{ $adherencePct }}%"></div>
            </div>
            <p class="text-xs text-gray-400 mt-2">
                @if($adherencePct >= 70) Good adherence — keep it up!
                @elseif($adherencePct >= 40) Moderate adherence — check in with patients.
                @else Low adherence — consider following up.
                @endif
            </p>
        </div>

        {{-- Patients without active plan alert --}}
        <div class="bg-white rounded-xl shadow p-6 lg:col-span-2">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
                <h2 class="text-base font-bold text-gray-800">Patients Without Active Plan</h2>
                <span class="ml-auto bg-amber-100 text-amber-800 text-xs font-bold px-2 py-0.5 rounded-full">{{ $patientsNoActivePlan->count() }}</span>
            </div>
            @if($patientsNoActivePlan->isEmpty())
            <p class="text-sm text-green-600 flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg> All patients have an active rehabilitation plan.</p>
            @else
            <div class="space-y-2 max-h-36 overflow-y-auto">
                @foreach($patientsNoActivePlan as $p)
                <div class="flex items-center justify-between py-1.5 border-b border-gray-100 last:border-0">
                    <span class="text-sm text-gray-700">{{ $p->user->name }}</span>
                    <a href="{{ route('clinician.plans.create', $p->id) }}" class="text-xs bg-sky-50 text-sky-700 hover:bg-sky-100 px-3 py-1 rounded-full font-medium">Create Plan</a>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Recent Feedback Notifications --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <h2 class="text-base font-bold text-gray-800">Recent Patient Feedback</h2>
                </div>
                <a href="{{ route('clinician.feedback.index') }}" class="text-xs text-sky-600 hover:underline font-medium">View all →</a>
            </div>
            @if($recentFeedback->isEmpty())
            <div class="px-6 py-8 text-center text-sm text-gray-500">No feedback received in the last 7 days.</div>
            @else
            <div class="divide-y divide-gray-100">
                @foreach($recentFeedback as $fb)
                @php
                $avgPain = \App\Models\PatientFeedback::where('patient_id', $fb->patient_id)
                ->where('rehab_plan_id', $fb->rehab_plan_id)
                ->where('is_plan_feedback', true)
                ->get()
                ->filter(fn($f) => $f->feedback_date && \Carbon\Carbon::parse($f->feedback_date)->format('Y-m-d H:i') === \Carbon\Carbon::parse($fb->feedback_date)->format('Y-m-d H:i'))
                ->avg('pain_level');
                $avgDiff = \App\Models\PatientFeedback::where('patient_id', $fb->patient_id)
                ->where('rehab_plan_id', $fb->rehab_plan_id)
                ->where('is_plan_feedback', true)
                ->get()
                ->filter(fn($f) => $f->feedback_date && \Carbon\Carbon::parse($f->feedback_date)->format('Y-m-d H:i') === \Carbon\Carbon::parse($fb->feedback_date)->format('Y-m-d H:i'))
                ->avg('difficulty_rating');
                $painColor = $avgPain >= 7 ? 'text-red-600 bg-red-50' : ($avgPain >= 4 ? 'text-amber-600 bg-amber-50' : 'text-green-600 bg-green-50');
                @endphp
                <div class="px-6 py-3 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-sky-100 flex items-center justify-center flex-shrink-0 text-sky-700 font-bold text-sm">
                        {{ strtoupper(substr($fb->patient->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $fb->patient->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $fb->rehabPlan->plan_name ?? 'Plan' }} · {{ \Carbon\Carbon::parse($fb->feedback_date)->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $painColor }}">Pain {{ round($avgPain, 1) }}/10</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold bg-indigo-50 text-indigo-600">Diff {{ round($avgDiff, 1) }}/5</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Patient Exercise Progress --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <h2 class="text-base font-bold text-gray-800">Patient Exercise Progress</h2>
                </div>
                <a href="{{ route('clinician.patients.index') }}" class="text-xs text-sky-600 hover:underline font-medium">View all →</a>
            </div>
            @if($patientStats->isEmpty())
            <div class="px-6 py-8 text-center text-sm text-gray-500">No patients yet.</div>
            @else
            <div class="divide-y divide-gray-100">
                @foreach($patientStats as $stat)
                <div class="px-6 py-3">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($stat['patient']->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $stat['patient']->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $stat['active_plan'] ? $stat['active_plan']->plan_name : 'No active plan' }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold {{ $stat['pct'] >= 70 ? 'text-green-600' : ($stat['pct'] >= 40 ? 'text-amber-500' : 'text-red-500') }}">
                            {{ $stat['pct'] }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                        <div class="h-1.5 rounded-full {{ $stat['pct'] >= 70 ? 'bg-green-400' : ($stat['pct'] >= 40 ? 'bg-amber-400' : 'bg-red-400') }}" style="width: {{ $stat['pct'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $stat['done'] }}/{{ $stat['total'] }} exercises completed</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Messages Section --}}
    @if($messages->count() > 0)
    <div class="bg-white rounded-xl shadow overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-base font-bold text-gray-800">System Messages</h2>
        </div>
        <div class="p-6 space-y-3">
            @foreach($messages as $message)
            <div class="flex items-start gap-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm text-green-800">{{ $message->message }}</p>
                    <p class="text-xs text-green-600 mt-1">{{ $message->created_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
                <form action="{{ route('clinician.messages.delete', $message->id) }}" method="POST" class="flex-shrink-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Dismiss</button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection