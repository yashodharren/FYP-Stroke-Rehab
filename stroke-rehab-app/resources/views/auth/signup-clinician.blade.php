@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('signup.role') }}" class="text-indigo-600 hover:text-indigo-800 font-medium mb-4 inline-block">← Back</a>
            <h1 class="text-4xl font-bold text-gray-900">Clinician Sign Up</h1>
            <p class="text-gray-600 mt-2">Create your account and start managing patient rehabilitation plans</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <form method="POST" action="{{ route('register.clinician') }}" class="space-y-6">
                @csrf

                <!-- Account Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h2>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" id="name" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror" placeholder="Dr. Jane Smith" value="{{ old('name') }}">
                        @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-500 @enderror" placeholder="jane@hospital.com" value="{{ old('email') }}">
                        @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                            <input type="password" id="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-red-500 @enderror" placeholder="••••••••">
                            <p class="text-gray-500 text-xs mt-1">Minimum 8 characters</p>
                            @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Professional Information</h2>

                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">License Number *</label>
                        <input type="text" id="license_number" name="license_number" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('license_number') border-red-500 @enderror" placeholder="LIC-123456" value="{{ old('license_number') }}">
                        <p class="text-gray-500 text-xs mt-1">Your medical/clinical license number</p>
                        @error('license_number')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">Specialization *</label>
                        <input type="text" id="specialization" name="specialization" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('specialization') border-red-500 @enderror" placeholder="e.g., Physiotherapy, Neurology, Occupational Therapy" value="{{ old('specialization') }}">
                        @error('specialization')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="hospital_affiliation" class="block text-sm font-medium text-gray-700 mb-2">Hospital/Clinic Affiliation</label>
                            <input type="text" id="hospital_affiliation" name="hospital_affiliation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('hospital_affiliation') border-red-500 @enderror" placeholder="City General Hospital" value="{{ old('hospital_affiliation') }}">
                            @error('hospital_affiliation')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                            <input type="tel" id="phone" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('phone') border-red-500 @enderror" placeholder="+1 (555) 123-4567" value="{{ old('phone') }}">
                            @error('phone')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Verification Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 9a1 1 0 100-2 1 1 0 000 2zm5-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-blue-900">Verification Required</h3>
                            <p class="text-sm text-blue-700 mt-1">Your account will be reviewed by an administrator. You'll receive an email once your credentials are verified.</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-medium transition-colors">
                        Create Account
                    </button>
                    <a href="{{ route('signup.role') }}" class="flex-1 bg-gray-300 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-400 font-medium text-center transition-colors">
                        Back
                    </a>
                </div>

                <p class="text-center text-gray-600 text-sm">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">Sign in</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
