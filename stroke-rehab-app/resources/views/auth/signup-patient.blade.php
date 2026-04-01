@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('signup.role') }}" class="text-blue-600 hover:text-blue-800 font-medium mb-4 inline-block">← Back</a>
            <h1 class="text-4xl font-bold text-gray-900">Patient Sign Up</h1>
            <p class="text-gray-600 mt-2">Create your account and start tracking your rehabilitation journey</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <form method="POST" action="{{ route('register.patient') }}" class="space-y-6">
                @csrf

                <!-- Account Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h2>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" id="name" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror" placeholder="John Doe" value="{{ old('name') }}">
                        @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror" placeholder="john@example.com" value="{{ old('email') }}">
                        @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                            <input type="password" id="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror" placeholder="••••••••">
                            <p class="text-gray-500 text-xs mt-1">Minimum 8 characters</p>
                            @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <!-- Clinical Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Clinical Information</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="age" class="block text-sm font-medium text-gray-700 mb-2">Age *</label>
                            <input type="number" id="age" name="age" required min="0" max="150" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('age') border-red-500 @enderror" placeholder="65" value="{{ old('age') }}">
                            @error('age')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                            <select id="gender" name="gender" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('gender') border-red-500 @enderror">
                                <option value="">Select gender</option>
                                <option value="0" @if(old('gender')=='0' ) selected @endif>Female</option>
                                <option value="1" @if(old('gender')=='1' ) selected @endif>Male</option>
                            </select>
                            @error('gender')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="rsbp" class="block text-sm font-medium text-gray-700 mb-2">Systolic Blood Pressure (mmHg)</label>
                            <input type="number" id="rsbp" name="rsbp" min="0" max="300" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rsbp') border-red-500 @enderror" placeholder="120" value="{{ old('rsbp') }}">
                            @error('rsbp')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stroke_subtype" class="block text-sm font-medium text-gray-700 mb-2">Stroke Type</label>
                            <select id="stroke_subtype" name="stroke_subtype" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('stroke_subtype') border-red-500 @enderror">
                                <option value="">Select stroke type</option>
                                <option value="TACS" @if(old('stroke_subtype')=='TACS' ) selected @endif>TACS (Total Anterior)</option>
                                <option value="PACS" @if(old('stroke_subtype')=='PACS' ) selected @endif>PACS (Partial Anterior)</option>
                                <option value="LACS" @if(old('stroke_subtype')=='LACS' ) selected @endif>LACS (Lacunar)</option>
                                <option value="POCS" @if(old('stroke_subtype')=='POCS' ) selected @endif>POCS (Posterior)</option>
                                <option value="OTH" @if(old('stroke_subtype')=='OTH' ) selected @endif>OTH (Other)</option>
                            </select>
                            @error('stroke_subtype')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="conscious_state" class="block text-sm font-medium text-gray-700 mb-2">Consciousness State</label>
                        <select id="conscious_state" name="conscious_state" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('conscious_state') border-red-500 @enderror">
                            <option value="">Select consciousness state</option>
                            <option value="Alert" @if(old('conscious_state')=='Alert' ) selected @endif>Alert</option>
                            <option value="Drowsy" @if(old('conscious_state')=='Drowsy' ) selected @endif>Drowsy</option>
                            <option value="Unconscious" @if(old('conscious_state')=='Unconscious' ) selected @endif>Unconscious</option>
                        </select>
                        @error('conscious_state')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Functional Deficits -->
                <div class="pb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Functional Deficits</h2>
                    <p class="text-gray-600 text-sm mb-4">Select any deficits you currently experience</p>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="rdef1" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @if(old('rdef1')) checked @endif>
                            <span class="ml-2 text-gray-700">Face Deficit</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="rdef2" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @if(old('rdef2')) checked @endif>
                            <span class="ml-2 text-gray-700">Arm/Hand Deficit</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="rdef3" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @if(old('rdef3')) checked @endif>
                            <span class="ml-2 text-gray-700">Leg/Foot Deficit</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="rdef4" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @if(old('rdef4')) checked @endif>
                            <span class="ml-2 text-gray-700">Speech Deficit</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="rdef5" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @if(old('rdef5')) checked @endif>
                            <span class="ml-2 text-gray-700">Vision Deficit</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="rdef6" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @if(old('rdef6')) checked @endif>
                            <span class="ml-2 text-gray-700">Visuospatial Deficit</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="rdef7" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @if(old('rdef7')) checked @endif>
                            <span class="ml-2 text-gray-700">Brainstem/Cerebellar</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="rdef8" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" @if(old('rdef8')) checked @endif>
                            <span class="ml-2 text-gray-700">Other Deficits</span>
                        </label>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium transition-colors">
                        Create Account
                    </button>
                    <a href="{{ route('signup.role') }}" class="flex-1 bg-gray-300 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-400 font-medium text-center transition-colors">
                        Back
                    </a>
                </div>

                <p class="text-center text-gray-600 text-sm">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Sign in</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection