@extends('layouts.clinician')

@section('page_title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="text-gray-600 mt-2">Here's your rehabilitation management overview</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Patients</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $patients->count() }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Active Plans</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $activePlans }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Completed Plans</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $completedPlans }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages Section -->
    @if($messages->count() > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">Messages</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($messages as $message)
                <div class="flex items-start gap-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex-shrink-0 mt-1">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-green-800">{{ $message->message }}</p>
                        <p class="text-xs text-green-600 mt-2">{{ $message->created_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                    <form action="{{ route('clinician.messages.delete', $message->id) }}" method="POST" class="flex-shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">Delete</button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">Appointment Reminders</h2>
            <a href="{{ route('clinician.appointments.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">View All</a>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-start gap-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex-shrink-0 mt-1">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-red-900">John Doe - Follow-up Session</h3>
                        <p class="text-sm text-red-700 mt-1">Today at 2:00 PM</p>
                        <p class="text-xs text-red-600 mt-2">Patient: Post-stroke rehabilitation progress check</p>
                    </div>
                    <button class="text-red-600 hover:text-red-800 font-medium text-sm">Reschedule</button>
                </div>

                <div class="flex items-start gap-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex-shrink-0 mt-1">
                        <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-yellow-900">Jane Smith - Assessment Review</h3>
                        <p class="text-sm text-yellow-700 mt-1">Tomorrow at 10:00 AM</p>
                        <p class="text-xs text-yellow-600 mt-2">Patient: Functional assessment and plan adjustment</p>
                    </div>
                    <button class="text-yellow-600 hover:text-yellow-800 font-medium text-sm">Reschedule</button>
                </div>

                <div class="flex items-start gap-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex-shrink-0 mt-1">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-blue-900">Michael Johnson - Exercise Session</h3>
                        <p class="text-sm text-blue-700 mt-1">April 5, 2026 at 3:00 PM</p>
                        <p class="text-xs text-blue-600 mt-2">Patient: Supervised rehabilitation exercise program</p>
                    </div>
                    <button class="text-blue-600 hover:text-blue-800 font-medium text-sm">Reschedule</button>
                </div>

                <div class="text-center py-4 text-gray-600 text-sm">
                    <p>No more appointments scheduled for this week</p>
                </div>
            </div>
        </div>
    </div>
    @endsection