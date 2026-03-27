@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900">My Patients</h1>
            <p class="text-gray-600 mt-2">Manage your assigned patients and their rehabilitation plans</p>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Age</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Stroke Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Deficit Area</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $patient)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $patient->user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $patient->age }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($patient->stroke_type) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $patient->deficit_area)) }}</td>
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
                                <td class="px-6 py-4 text-sm space-x-2">
                                    <a href="{{ route('clinician.patients.show', $patient->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-600">No patients assigned yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
