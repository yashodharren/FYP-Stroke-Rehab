@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Stroke Rehab</h1>
            <p class="text-gray-600">Create your account</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-8 space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 text-center">Choose Your Role</h2>
            <p class="text-center text-gray-600">Select whether you're a patient or a healthcare clinician</p>

            <div class="grid grid-cols-1 gap-4">
                <!-- Patient Sign-up -->
                <a href="{{ route('signup.patient') }}" class="group relative overflow-hidden rounded-lg border-2 border-blue-200 p-6 hover:border-blue-500 hover:shadow-lg transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative">
                        <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-blue-100 group-hover:bg-blue-200 transition-colors mb-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">I'm a Patient</h3>
                        <p class="text-sm text-gray-600">Sign up to track your rehabilitation progress and view your personalized exercise plans</p>
                    </div>
                </a>

                <!-- Clinician Sign-up -->
                <a href="{{ route('signup.clinician') }}" class="group relative overflow-hidden rounded-lg border-2 border-indigo-200 p-6 hover:border-indigo-500 hover:shadow-lg transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative">
                        <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-indigo-100 group-hover:bg-indigo-200 transition-colors mb-4">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">I'm a Clinician</h3>
                        <p class="text-sm text-gray-600">Sign up to create and manage personalized rehabilitation plans for your patients</p>
                    </div>
                </a>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <p class="text-center text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
