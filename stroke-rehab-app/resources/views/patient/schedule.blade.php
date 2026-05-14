@extends('layouts.patient')

@section('title', 'Patient Schedule')
@section('page_title', 'Weekly Rehabilitation Schedule')

@section('content')
@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
    {{ session('success') }}
</div>
@endif

@if($activePlan)
@php
$todayName = \Carbon\Carbon::now()->format('l');
$weekDays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
// Build Mon=0..Sun=6 offset from this week's Monday
$monday = \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);
$dayDates = [];
foreach($weekDays as $i => $d) {
$dayDates[$d] = $monday->copy()->addDays($i);
}
@endphp
<!-- Calendar View Toggle -->
<div class="mb-6 flex gap-4">
    <button onclick="showCalendarView()" id="calendarViewBtn" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
        📅 Calendar View
    </button>
    <button onclick="showListView()" id="listViewBtn" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium">
        📋 List View
    </button>
</div>

<!-- Calendar View (Time Grid) -->
<div id="calendarView" class="bg-white rounded-lg shadow overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gradient-to-r from-green-500 to-teal-500">
                    <th class="border border-gray-300 px-4 py-3 text-white font-semibold text-left w-24">Time</th>
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                    <th class="border border-gray-300 px-4 py-3 font-semibold text-center {{ $day === $todayName ? 'bg-white bg-opacity-20' : '' }}">
                        <span class="block text-white text-sm">{{ substr($day, 0, 3) }}</span>
                        <span class="block text-xs {{ $day === $todayName ? 'text-yellow-200 font-bold' : 'text-green-100' }}">{{ $dayDates[$day]->format('M j') }}</span>
                        @if($day === $todayName)<span class="block text-xs text-yellow-300 font-bold">Today</span>@endif
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($schedule['byTime'] as $timeSlot => $dayExercises)
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-4 py-3 font-semibold text-gray-900 bg-gray-50">{{ $timeSlot }}</td>
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                    <td class="border border-gray-300 px-4 py-3 text-center">
                        @if(isset($dayExercises[$day]) && $dayExercises[$day])
                        @php
                        $pe = $dayExercises[$day];
                        $exerciseName = $pe->exercise->name;
                        $colorClass = getExerciseColorClass($exerciseName);
                        @endphp
                        <div class="{{ $colorClass['bg'] }} {{ $colorClass['border'] }} border-l-4 p-2 rounded text-left {{ $pe->is_completed ? 'opacity-60' : '' }}">
                            <div class="flex items-center justify-between mb-1">
                                <p class="font-semibold text-sm text-gray-900 {{ $pe->is_completed ? 'line-through' : '' }}">{{ $exerciseName }}</p>
                                @if($pe->is_completed)
                                <span class="text-green-600 text-xs font-bold">✓</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600">
                                {{ $pe->custom_duration_minutes ?? $pe->exercise->duration_minutes }} min
                            </p>
                            <p class="text-xs text-gray-600">
                                {{ $pe->custom_repetitions ?? $pe->exercise->repetitions }} reps
                            </p>
                            <div class="flex gap-1 mt-2">
                                <button onclick="showExerciseDetails({{ $pe->id }})" class="text-xs {{ $colorClass['text'] }} hover:opacity-80 font-medium">
                                    View
                                </button>
                                @if($day === $todayName)
                                <form method="POST" action="{{ route('patient.mark-done', $pe->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs {{ $pe->is_completed ? 'text-gray-500' : 'text-green-600' }} hover:opacity-80 font-medium">
                                        {{ $pe->is_completed ? 'Undo' : 'Done' }}
                                    </button>
                                </form>
                                @elseif($pe->is_completed)
                                <span class="text-xs text-green-600 font-medium">✓ Done</span>
                                @else
                                <span class="text-xs text-gray-400 cursor-not-allowed" title="Only today's exercises can be marked">🔒</span>
                                @endif
                            </div>
                        </div>
                        @else
                        <span class="text-gray-400 text-sm">—</span>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- List View (Day-based) -->
<div id="listView" class="hidden grid grid-cols-1 gap-6">
    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-teal-500 px-6 py-4 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white">{{ $day }}</h2>
                <p class="text-green-100 text-sm mt-0.5">{{ $dayDates[$day]->format('F j, Y') }}</p>
            </div>
            @if($day === $todayName)
            <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full">Today</span>
            @endif
        </div>
        <div class="p-6">
            @if($schedule['byDay'][$day]->count() > 0)
            <div class="space-y-4">
                @foreach($schedule['byDay'][$day] as $planExercise)
                <div class="border {{ $planExercise->is_completed ? 'border-green-300 bg-green-50' : 'border-gray-200' }} rounded-lg p-4 transition-all" data-plan-exercise-id="{{ $planExercise->id }}">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 {{ $planExercise->is_completed ? 'line-through text-gray-500' : '' }}">{{ $planExercise->exercise->name }}</h3>
                            <p class="text-gray-600 text-sm mt-1">{{ $planExercise->exercise->description }}</p>
                            @if($planExercise->is_completed && $planExercise->completed_at)
                            <p class="text-green-600 text-xs mt-1">✓ Completed {{ $planExercise->completed_at->format('M d, Y \a\t H:i') }}</p>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $planExercise->frequency_per_week }}x/week
                            </span>
                            @if($planExercise->is_completed)
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Done ✓</span>
                            @endif
                        </div>
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
                            <p class="font-semibold text-gray-900">{{ substr($planExercise->scheduled_time, 0, 5) }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 p-3 rounded mb-4">
                        <p class="text-sm text-gray-700"><strong>Instructions:</strong> {{ $planExercise->exercise->instructions }}</p>
                    </div>

                    <div class="border-t border-gray-200 pt-4 flex flex-wrap gap-3 items-start">
                        @if($day === $todayName)
                        <form method="POST" action="{{ route('patient.mark-done', $planExercise->id) }}">
                            @csrf
                            <button type="submit"
                                class="{{ $planExercise->is_completed ? 'bg-gray-200 text-gray-600 hover:bg-gray-300' : 'bg-green-600 text-white hover:bg-green-700' }} px-4 py-2 rounded-lg font-medium text-sm inline-flex items-center gap-2">
                                @if($planExercise->is_completed)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Undo Done
                                @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Mark as Done
                                @endif
                            </button>
                        </form>
                        @elseif($planExercise->is_completed)
                        <span class="px-4 py-2 bg-green-100 text-green-700 rounded-lg font-medium text-sm inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Completed
                        </span>
                        @else
                        <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg font-medium text-sm inline-flex items-center gap-2 cursor-not-allowed" title="Only today's exercises can be marked as done">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            {{ $day === $todayName ? 'Mark as Done' : 'Locked' }}
                        </span>
                        @endif
                        <button type="button" onclick="toggleReschedule({{ $planExercise->id }})"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium text-sm inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Reschedule
                        </button>

                        <form id="reschedule-form-{{ $planExercise->id }}" method="POST"
                            action="{{ route('patient.reschedule', $planExercise->id) }}"
                            class="hidden mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">New Day</label>
                                    <select name="day_of_week" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d)
                                        <option value="{{ $d }}" @selected($planExercise->day_of_week === $d)>{{ $d }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">New Time</label>
                                    <input type="time" name="scheduled_time" required
                                        value="{{ $planExercise->scheduled_time ? substr($planExercise->scheduled_time, 0, 5) : '09:00' }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="flex items-end gap-2">
                                    <button type="submit"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium text-sm">
                                        Save
                                    </button>
                                    <button type="button" onclick="toggleReschedule({{ $planExercise->id }})"
                                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 font-medium text-sm">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
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

<script>
    function showCalendarView() {
        document.getElementById('calendarView').classList.remove('hidden');
        document.getElementById('listView').classList.add('hidden');
        document.getElementById('calendarViewBtn').classList.remove('bg-gray-300', 'text-gray-700');
        document.getElementById('calendarViewBtn').classList.add('bg-green-600', 'text-white');
        document.getElementById('listViewBtn').classList.remove('bg-green-600', 'text-white');
        document.getElementById('listViewBtn').classList.add('bg-gray-300', 'text-gray-700');
    }

    function showListView() {
        document.getElementById('calendarView').classList.add('hidden');
        document.getElementById('listView').classList.remove('hidden');
        document.getElementById('listViewBtn').classList.remove('bg-gray-300', 'text-gray-700');
        document.getElementById('listViewBtn').classList.add('bg-green-600', 'text-white');
        document.getElementById('calendarViewBtn').classList.remove('bg-green-600', 'text-white');
        document.getElementById('calendarViewBtn').classList.add('bg-gray-300', 'text-gray-700');
    }

    function toggleReschedule(planExerciseId) {
        const form = document.getElementById('reschedule-form-' + planExerciseId);
        if (form) form.classList.toggle('hidden');
    }

    function showExerciseDetails(planExerciseId) {
        // Scroll to the exercise details in the list view
        const exerciseElement = document.querySelector(`[data-plan-exercise-id="${planExerciseId}"]`);
        if (exerciseElement) {
            showListView();
            exerciseElement.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            exerciseElement.classList.add('ring-2', 'ring-green-500');
            setTimeout(() => {
                exerciseElement.classList.remove('ring-2', 'ring-green-500');
            }, 3000);
        }
    }
</script>

@php
function getExerciseColorClass($exerciseName) {
$colorMap = [
'Shoulder Shrug' => ['bg' => 'bg-green-50', 'border' => 'border-green-500', 'text' => 'text-green-600'],
'Hand and Wrist Stretch' => ['bg' => 'bg-orange-50', 'border' => 'border-orange-500', 'text' => 'text-orange-600'],
'Seated Marching' => ['bg' => 'bg-red-50', 'border' => 'border-red-500', 'text' => 'text-red-600'],
'Towel Slide' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-500', 'text' => 'text-purple-600'],
'Sit-to-Stand' => ['bg' => 'bg-pink-50', 'border' => 'border-pink-500', 'text' => 'text-pink-600'],
'Finger Tapping' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-500', 'text' => 'text-yellow-600'],
'Wall Push-ups' => ['bg' => 'bg-cyan-50', 'border' => 'border-cyan-500', 'text' => 'text-cyan-600'],
'Squeeze Ball' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-500', 'text' => 'text-blue-600'],
'Heel-to-Toe Stand' => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-500', 'text' => 'text-indigo-600'],
'Single Leg Stance' => ['bg' => 'bg-lime-50', 'border' => 'border-lime-500', 'text' => 'text-lime-600'],
'Facial Muscle Exercises' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-500', 'text' => 'text-rose-600'],
'Cheek Puffing' => ['bg' => 'bg-fuchsia-50', 'border' => 'border-fuchsia-500', 'text' => 'text-fuchsia-600'],
'Tongue Exercises' => ['bg' => 'bg-violet-50', 'border' => 'border-violet-500', 'text' => 'text-violet-600'],
'Lip Rounding' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-500', 'text' => 'text-amber-600'],
'Speech Repetition' => ['bg' => 'bg-sky-50', 'border' => 'border-sky-500', 'text' => 'text-sky-600'],
'Counting Exercise' => ['bg' => 'bg-teal-50', 'border' => 'border-teal-500', 'text' => 'text-teal-600'],
'Swallowing Exercise' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-500', 'text' => 'text-emerald-600'],
'Neck Stretches' => ['bg' => 'bg-slate-50', 'border' => 'border-slate-500', 'text' => 'text-slate-600'],
'Eye Tracking' => ['bg' => 'bg-zinc-50', 'border' => 'border-zinc-500', 'text' => 'text-zinc-600'],
'Visual Scanning' => ['bg' => 'bg-stone-50', 'border' => 'border-stone-500', 'text' => 'text-stone-600'],
'Attention Focus' => ['bg' => 'bg-red-50', 'border' => 'border-red-500', 'text' => 'text-red-600'],
'Memory Recall' => ['bg' => 'bg-orange-50', 'border' => 'border-orange-500', 'text' => 'text-orange-600'],
'Puzzle Solving' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-500', 'text' => 'text-yellow-600'],
'Reading Exercise' => ['bg' => 'bg-green-50', 'border' => 'border-green-500', 'text' => 'text-green-600'],
'Emotional Expression' => ['bg' => 'bg-cyan-50', 'border' => 'border-cyan-500', 'text' => 'text-cyan-600'],
'Deep Breathing' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-500', 'text' => 'text-blue-600'],
'Relaxation Technique' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-500', 'text' => 'text-purple-600'],
'Guided Imagery' => ['bg' => 'bg-pink-50', 'border' => 'border-pink-500', 'text' => 'text-pink-600'],
'Knee Lifts' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-500', 'text' => 'text-rose-600'],
'Heel Slides' => ['bg' => 'bg-fuchsia-50', 'border' => 'border-fuchsia-500', 'text' => 'text-fuchsia-600'],
'Glute Squeezes' => ['bg' => 'bg-violet-50', 'border' => 'border-violet-500', 'text' => 'text-violet-600'],
'Calf Raises' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-500', 'text' => 'text-amber-600'],
'Step-Ups' => ['bg' => 'bg-sky-50', 'border' => 'border-sky-500', 'text' => 'text-sky-600'],
'Tandem Walking' => ['bg' => 'bg-teal-50', 'border' => 'border-teal-500', 'text' => 'text-teal-600'],
'Sit and Reach' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-500', 'text' => 'text-emerald-600'],
'Hip Flexor Stretch' => ['bg' => 'bg-slate-50', 'border' => 'border-slate-500', 'text' => 'text-slate-600'],
'Arm Circles' => ['bg' => 'bg-zinc-50', 'border' => 'border-zinc-500', 'text' => 'text-zinc-600'],
'Bicep Curls' => ['bg' => 'bg-stone-50', 'border' => 'border-stone-500', 'text' => 'text-stone-600'],
'Tricep Extensions' => ['bg' => 'bg-red-50', 'border' => 'border-red-500', 'text' => 'text-red-600'],
'Wrist Flexion' => ['bg' => 'bg-orange-50', 'border' => 'border-orange-500', 'text' => 'text-orange-600'],
'Pronation Supination' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-500', 'text' => 'text-yellow-600'],
'Shoulder Abduction' => ['bg' => 'bg-green-50', 'border' => 'border-green-500', 'text' => 'text-green-600'],
'Shoulder Flexion' => ['bg' => 'bg-cyan-50', 'border' => 'border-cyan-500', 'text' => 'text-cyan-600'],
'Seated Marching Advanced' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-500', 'text' => 'text-blue-600'],
'Standing Balance' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-500', 'text' => 'text-purple-600'],
'Gait Training' => ['bg' => 'bg-pink-50', 'border' => 'border-pink-500', 'text' => 'text-pink-600'],
'Stair Training' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-500', 'text' => 'text-rose-600'],
'Coordination Drill' => ['bg' => 'bg-fuchsia-50', 'border' => 'border-fuchsia-500', 'text' => 'text-fuchsia-600'],
'Rapid Alternating' => ['bg' => 'bg-violet-50', 'border' => 'border-violet-500', 'text' => 'text-violet-600'],
'Cross Body Reach' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-500', 'text' => 'text-amber-600'],
];

return $colorMap[$exerciseName] ?? [
'bg' => 'bg-gray-50',
'border' => 'border-gray-500',
'text' => 'text-gray-600'
];
}
@endphp

@endsection