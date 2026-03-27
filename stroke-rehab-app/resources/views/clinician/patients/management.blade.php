@extends('layouts.clinician')

@section('page_title', 'My Patients')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Patients</h1>
            <p class="text-gray-600 mt-2">Manage your assigned patients and add new ones</p>
        </div>
        <a href="{{ route('clinician.patients.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">
            + Add New Patient
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
        {{ session('error') }}
    </div>
    @endif

    <!-- Search Section -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Search & Assign Existing Patients</h2>
        <form method="GET" action="{{ route('clinician.patients.index') }}" class="flex gap-3">
            <input type="text" name="search" placeholder="Search by name or email..." value="{{ $search }}"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium">
                Search
            </button>
            @if($search)
            <a href="{{ route('clinician.patients.index') }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-400 font-medium">
                Clear
            </a>
            @endif
        </form>

        @if($search && count($unassignedPatients) > 0)
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Search Results</h3>
            <div class="space-y-3">
                @foreach($unassignedPatients as $user)
                <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div>
                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('clinician.patients.assign', $user->id) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-medium">
                            Assign to My Care
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @elseif($search && count($unassignedPatients) === 0)
        <div class="mt-6 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded">
            No unassigned patients found matching your search.
        </div>
        @endif
    </div>

    <!-- My Patients Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Your Assigned Patients ({{ count($patients) }})</h2>
        </div>

        @if(count($patients) > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Age</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Recovery Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Rehab Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Patient Info</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $patient->user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $patient->user->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $patient->age }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if($patient->recovery_status === 'new') bg-blue-100 text-blue-800
                                @elseif($patient->recovery_status === 'in_progress') bg-yellow-100 text-yellow-800
                                @elseif($patient->recovery_status === 'completed') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $patient->recovery_status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @php
                            $activePlan = $patient->rehabPlans()->where('status', 'active')->first();
                            @endphp
                            @if($activePlan)
                            <a href="{{ route('clinician.plans.edit', $activePlan->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                {{ $activePlan->plan_name }}
                            </a>
                            @else
                            <a href="{{ route('clinician.plans.create', $patient->id) }}" class="bg-green-600 text-white px-3 py-1 rounded text-xs font-medium hover:bg-green-700">
                                Create Plan
                            </a>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('clinician.patients.show', $patient->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">View</a>
                            <a href="{{ route('clinician.patients.edit', $patient->id) }}" class="text-purple-600 hover:text-purple-800 font-medium">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-8 text-center text-gray-600">
            <p>No patients assigned yet.</p>
            <p class="text-sm mt-2">
                <a href="{{ route('clinician.patients.create') }}" class="text-blue-600 hover:text-blue-800 font-medium">Create a new patient</a>
                or search for existing patients to assign to your care.
            </p>
        </div>
        @endif
    </div>
</div>
@endsection