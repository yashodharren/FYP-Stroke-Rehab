@extends('layouts.patient')

@section('title', 'Patient Appointments')
@section('page_title', 'My Appointments')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-medium">Upcoming Appointments</p>
        <p class="text-3xl font-bold text-green-600 mt-2">0</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-medium">Completed Appointments</p>
        <p class="text-3xl font-bold text-blue-600 mt-2">0</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">Appointment Schedule</h2>
    </div>
    <div class="p-6">
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Appointments Scheduled</h3>
            <p class="text-gray-600">Your clinician will schedule appointments with you. Check back soon!</p>
        </div>
    </div>
</div>

<div class="mt-8 bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">Appointment Guidelines</h2>
    </div>
    <div class="p-6 space-y-4 text-gray-700">
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-8 w-8 rounded-md bg-green-500 text-white">
                    ✓
                </div>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">Be on Time</h3>
                <p class="text-sm text-gray-600">Please arrive 5-10 minutes before your scheduled appointment time.</p>
            </div>
        </div>

        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-8 w-8 rounded-md bg-green-500 text-white">
                    ✓
                </div>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">Bring Your Records</h3>
                <p class="text-sm text-gray-600">Bring any relevant medical records or exercise logs to your appointment.</p>
            </div>
        </div>

        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-8 w-8 rounded-md bg-green-500 text-white">
                    ✓
                </div>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">Communicate Concerns</h3>
                <p class="text-sm text-gray-600">Let your clinician know about any pain, discomfort, or concerns during your exercises.</p>
            </div>
        </div>

        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-8 w-8 rounded-md bg-green-500 text-white">
                    ✓
                </div>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">Ask Questions</h3>
                <p class="text-sm text-gray-600">Don't hesitate to ask questions about your rehabilitation plan or exercises.</p>
            </div>
        </div>
    </div>
</div>
@endsection
