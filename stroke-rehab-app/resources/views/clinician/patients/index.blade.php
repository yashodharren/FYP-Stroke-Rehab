@extends('layouts.clinician')

@section('page_title', '')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:justify-end gap-4">
        <a href="{{ route('clinician.patients.create') }}"
            class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add New Patient
        </a>
    </div>

    {{-- Search & Assign Panel --}}
    <div class="bg-white rounded-2xl shadow-sm border-t-4 border-t-emerald-400 p-6">
        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Search & Assign Existing Patient</h2>
        <form method="GET" action="{{ route('clinician.patients.index') }}" class="flex gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                </svg>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Search by name or email…"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 bg-gray-50">
            </div>
            <button type="submit"
                class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl transition">
                Search
            </button>
            @if($search)
            <a href="{{ route('clinician.patients.index') }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-5 py-2.5 rounded-xl transition">
                Clear
            </a>
            @endif
        </form>

        @if($search && count($unassignedPatients) > 0)
        <div class="mt-5 space-y-2">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-2">Results</p>
            @foreach($unassignedPatients as $user)
            <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center text-sm font-bold uppercase">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('clinician.patients.assign', $user->id) }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="text-sm bg-teal-600 hover:bg-teal-700 text-white px-4 py-1.5 rounded-lg font-medium transition">
                        Assign to My Care
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @elseif($search && count($unassignedPatients) === 0)
        <div class="mt-4 flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 text-sm px-4 py-3 rounded-xl">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 1 1 0 20A10 10 0 0 1 12 2z" />
            </svg>
            No unassigned patients found matching your search.
        </div>
        @endif
    </div>

    {{-- Patient Cards Grid --}}
    @if(count($patients) > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($patients as $patient)
        @php
        $activePlan = $patient->rehabPlans()->where('status', 'active')->first();
        $latestPlan = $activePlan ?? $patient->rehabPlans()->orderByDesc('id')->first();
        $planStatus = $latestPlan->status ?? null;
        $initials = collect(explode(' ', $patient->user->name))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->implode('');
        $avatarColors = ['bg-teal-100 text-teal-700', 'bg-blue-100 text-blue-700', 'bg-violet-100 text-violet-700', 'bg-rose-100 text-rose-700', 'bg-amber-100 text-amber-700'];
        $avatarColor = $avatarColors[$patient->id % count($avatarColors)];
        $borderAccent = match($planStatus) {
        'active' => 'border-t-4 border-t-emerald-400',
        'draft' => 'border-t-4 border-t-gray-300',
        'completed' => 'border-t-4 border-t-blue-400',
        'paused' => 'border-t-4 border-t-amber-400',
        default => 'border-t-4 border-t-gray-200',
        };
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 {{ $borderAccent }} hover:shadow-md transition-shadow flex flex-col">

            {{-- Card Header --}}
            <div class="flex items-center gap-4 px-5 pt-5 pb-4 border-b border-gray-50">
                <div class="w-12 h-12 rounded-full {{ $avatarColor }} flex items-center justify-center text-lg font-bold flex-shrink-0">
                    {{ $initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate">{{ $patient->user->name }}</p>
                    <p class="text-xs text-gray-600 mt-0.5">{{ $patient->user->email }}</p>
                </div>
                {{-- Plan status badge --}}
                @if($planStatus === 'active')
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex-shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                </span>
                @elseif($planStatus === 'draft')
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200 flex-shrink-0">Draft</span>
                @elseif($planStatus === 'completed')
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 flex-shrink-0">Completed</span>
                @elseif($planStatus === 'paused')
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 flex-shrink-0">Paused</span>
                @else
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200 flex-shrink-0">No Plan</span>
                @endif
            </div>

            {{-- Card Body --}}
            <div class="px-5 py-4 flex-1 grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-xs text-gray-600 mb-0.5">Age</p>
                    <p class="font-medium text-gray-800">{{ $patient->age ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 mb-0.5">Stroke Type</p>
                    <p class="font-medium text-gray-800">{{ $patient->stroke_subtype ?? '—' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-600 mb-0.5">Rehab Plan</p>
                    @if($activePlan)
                    <div class="flex items-center gap-2">
                        <a href="{{ route('clinician.plans.edit', $activePlan->id) }}"
                            class="text-teal-600 hover:text-teal-800 font-medium truncate max-w-[150px]">
                            {{ $activePlan->plan_name }}
                        </a>
                        <button type="button"
                            onclick="deletePlan({{ $activePlan->id }}, '{{ addslashes($activePlan->plan_name) }}')"
                            class="text-red-400 hover:text-red-600 transition flex-shrink-0" title="Delete plan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                    @else
                    <a href="{{ route('clinician.plans.create', $patient->id) }}"
                        class="inline-flex items-center gap-1 text-xs bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-lg font-medium transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Plan
                    </a>
                    @endif
                </div>
            </div>

            {{-- Card Footer Actions --}}
            <div class="px-5 pb-5 flex gap-2">
                <a href="{{ route('clinician.patients.show', $patient->id) }}"
                    class="flex-1 text-center text-sm font-medium bg-teal-600 hover:bg-teal-700 text-white py-2 rounded-xl transition">
                    View
                </a>
                <a href="{{ route('clinician.patients.edit', $patient->id) }}"
                    class="flex-1 text-center text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-xl transition">
                    Edit
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-8 py-16 text-center">
        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.768-.231-1.48-.634-2.073M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.768.231-1.48.634-2.073M9 12a4 4 0 108 0 4 4 0 00-8 0zm8 0v1m-4-1v1" />
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-700 mb-1">No patients yet</h3>
        <p class="text-sm text-gray-600 mb-6">Get started by creating a new patient or searching for existing ones to assign.</p>
        <a href="{{ route('clinician.patients.create') }}"
            class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add First Patient
        </a>
    </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deletePlan(planId, planName) {
        Swal.fire({
            title: 'Delete Rehabilitation Plan?',
            text: 'Are you sure you want to delete "' + planName + '"? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete Plan',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/clinician/plans/' + planId + '/delete';
                form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                    '<input type="hidden" name="_method" value="DELETE">';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection