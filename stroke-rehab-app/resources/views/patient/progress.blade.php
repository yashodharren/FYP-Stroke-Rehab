@extends('layouts.patient')

@section('title', 'My Progress')
@section('page_title', 'My Progress')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@if(!$activePlan)
<div class="bg-white rounded-lg shadow p-12 text-center">
    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
    </svg>
    <h3 class="text-lg font-semibold text-gray-700 mb-1">No Active Plan</h3>
    <p class="text-gray-500 text-sm">Progress will appear here once you have an active rehabilitation plan.</p>
</div>
@else

{{-- Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
        <p class="text-gray-500 text-sm font-medium">Completed</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ $completedExercises }}</p>
        <p class="text-xs text-gray-400 mt-1">exercises done</p>
    </div>
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-red-400">
        <p class="text-gray-500 text-sm font-medium">Missed</p>
        <p class="text-3xl font-bold text-red-500 mt-1">{{ $missedExercises }}</p>
        <p class="text-xs text-gray-400 mt-1">not yet completed</p>
    </div>
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
        <p class="text-gray-500 text-sm font-medium">Total</p>
        <p class="text-3xl font-bold text-blue-600 mt-1">{{ $totalExercises }}</p>
        <p class="text-xs text-gray-400 mt-1">scheduled exercises</p>
    </div>
    <div class="bg-white rounded-xl shadow p-6 border-l-4 {{ $completionRate >= 60 ? 'border-emerald-500' : 'border-yellow-400' }}">
        <p class="text-gray-500 text-sm font-medium">Completion Rate</p>
        <p class="text-3xl font-bold {{ $completionRate >= 60 ? 'text-emerald-600' : 'text-yellow-600' }} mt-1">{{ $completionRate }}%</p>
        <div class="mt-2 bg-gray-200 rounded-full h-2">
            <div class="h-2 rounded-full {{ $completionRate >= 60 ? 'bg-emerald-500' : 'bg-yellow-400' }}" style="width: {{ $completionRate }}%"></div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    {{-- Done vs Missed per Day Bar Chart --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Exercises Done vs Missed by Day</h2>
        <canvas id="dailyBarChart" height="220"></canvas>
    </div>

    {{-- Overall Doughnut Chart --}}
    <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
        <h2 class="text-lg font-bold text-gray-900 mb-4 self-start">Overall Completion</h2>
        <div class="relative w-56 h-56">
            <canvas id="completionDoughnut"></canvas>
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-3xl font-bold {{ $completionRate >= 60 ? 'text-emerald-600' : 'text-yellow-600' }}">{{ $completionRate }}%</span>
                <span class="text-xs text-gray-500 mt-1">completed</span>
            </div>
        </div>
        <div class="flex gap-6 mt-4 text-sm">
            <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>Done ({{ $completedExercises }})</span>
            <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span>Missed ({{ $missedExercises }})</span>
        </div>
    </div>
</div>

{{-- Completion Rate per Day Bar --}}
<div class="bg-white rounded-xl shadow p-6 mb-8">
    <h2 class="text-lg font-bold text-gray-900 mb-4">Completion Rate per Day</h2>
    <canvas id="rateBarChart" height="100"></canvas>
</div>

{{-- Per-Exercise Breakdown Table --}}
<div class="bg-white rounded-xl shadow overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-500 to-teal-500">
        <h2 class="text-lg font-bold text-white">Exercise Breakdown</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-left">
                    <th class="px-6 py-3 font-medium">Exercise</th>
                    <th class="px-6 py-3 font-medium">Day</th>
                    <th class="px-6 py-3 font-medium">Target Area</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Completed At</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($exerciseStats as $ex)
                <tr class="hover:bg-gray-50 {{ $ex['completed'] ? 'bg-green-50' : '' }}">
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $ex['name'] }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $ex['day'] }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ ucfirst(str_replace('_', ' ', $ex['target_area'])) }}</td>
                    <td class="px-6 py-3">
                        @if($ex['completed'])
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">✓ Done</span>
                        @else
                        <span class="px-2 py-1 bg-red-100 text-red-600 rounded-full text-xs font-medium">✗ Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $ex['completed_at'] ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@php
$days = array_keys($dailyStats);
$doneData = array_column($dailyStats, 'done');
$missedData = array_column($dailyStats, 'missed');
$rateData = array_column($dailyStats, 'rate');
@endphp

<script>
const days = @json($days);
const doneData = @json($doneData);
const missedData = @json($missedData);
const rateData = @json($rateData);

// Done vs Missed grouped bar chart
new Chart(document.getElementById('dailyBarChart'), {
    type: 'bar',
    data: {
        labels: days,
        datasets: [
            {
                label: 'Done',
                data: doneData,
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 1,
                borderRadius: 4,
            },
            {
                label: 'Missed',
                data: missedData,
                backgroundColor: 'rgba(248, 113, 113, 0.8)',
                borderColor: 'rgba(248, 113, 113, 1)',
                borderWidth: 1,
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: { mode: 'index', intersect: false }
        },
        scales: {
            x: { grid: { display: false } },
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1, precision: 0 },
                title: { display: true, text: 'No. of Exercises' }
            }
        }
    }
});

// Overall doughnut
new Chart(document.getElementById('completionDoughnut'), {
    type: 'doughnut',
    data: {
        labels: ['Done', 'Missed'],
        datasets: [{
            data: [{{ $completedExercises }}, {{ $missedExercises }}],
            backgroundColor: ['rgba(16,185,129,0.85)', 'rgba(248,113,113,0.75)'],
            borderColor: ['#10b981', '#f87171'],
            borderWidth: 2,
            hoverOffset: 6,
        }]
    },
    options: {
        cutout: '70%',
        plugins: { legend: { display: false }, tooltip: { enabled: true } }
    }
});

// Completion rate line/bar per day
new Chart(document.getElementById('rateBarChart'), {
    type: 'bar',
    data: {
        labels: days,
        datasets: [{
            label: 'Completion Rate (%)',
            data: rateData,
            backgroundColor: rateData.map(r => r >= 60 ? 'rgba(16,185,129,0.8)' : 'rgba(251,191,36,0.8)'),
            borderColor: rateData.map(r => r >= 60 ? '#10b981' : '#f59e0b'),
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ctx.parsed.y + '%' } }
        },
        scales: {
            x: { grid: { display: false } },
            y: {
                beginAtZero: true,
                max: 100,
                ticks: { callback: v => v + '%' },
                title: { display: true, text: 'Completion Rate' }
            }
        }
    }
});
</script>
@endif
@endsection
