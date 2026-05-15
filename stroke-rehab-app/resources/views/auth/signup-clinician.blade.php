@extends('layouts.app')
@section('hide_nav', true)

@section('content')
<div class="min-h-screen bg-slate-50 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-4xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row">

            {{-- Left branding panel --}}
            <div class="bg-slate-900 text-white md:w-5/12 flex flex-col items-center justify-center px-10 py-12 gap-6">
                <img src="{{ asset('images/logo.png') }}" alt="StrokeRehab Logo" class="w-44 h-44 object-contain">
                <div class="text-center">
                    <h1 class="text-2xl font-bold tracking-tight">StrokeRehab</h1>
                    <p class="text-slate-400 text-sm mt-2 leading-relaxed">Intelligent Post-Stroke<br>Rehabilitation Plan Generator</p>
                </div>
                <div class="mt-2 p-4 bg-slate-800 rounded-xl w-full">
                    <div class="flex gap-3 items-start">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Verification Required</p>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">Your account will be reviewed by an administrator before activation.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right form panel --}}
            <div class="md:w-7/12 px-10 py-10 overflow-y-auto max-h-screen">
                <div class="mb-6">
                    <a href="{{ route('signup.role') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 mb-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </a>
                    <h2 class="text-2xl font-bold text-slate-800">Clinician Sign Up</h2>
                    <p class="text-slate-500 text-sm mt-1">Create your account and start managing patient rehabilitation plans</p>
                </div>

                <form method="POST" action="{{ route('register.clinician') }}" class="space-y-5">
                    @csrf

                    {{-- Account Information --}}
                    <div class="border-b border-slate-100 pb-5">
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-4">Account Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Full Name *</label>
                                <input type="text" id="name" name="name" required value="{{ old('name') }}" placeholder="Dr. Jane Smith"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-400 @enderror">
                                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address *</label>
                                <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="jane@hospital.com"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('email') border-red-400 @enderror">
                                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password *</label>
                                    <input type="password" id="password" name="password" required placeholder="••••••••"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('password') border-red-400 @enderror">
                                    <p class="text-slate-400 text-xs mt-1">Minimum 8 characters</p>
                                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm Password *</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Professional Information --}}
                    <div class="pb-5">
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-4">Professional Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="specialization" class="block text-sm font-medium text-slate-700 mb-1.5">Specialization *</label>
                                <input type="text" id="specialization" name="specialization" required value="{{ old('specialization') }}" placeholder="e.g., Physiotherapy, Neurology"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('specialization') border-red-400 @enderror">
                                @error('specialization')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="hospital_affiliation" class="block text-sm font-medium text-slate-700 mb-1.5">Hospital/Clinic</label>
                                    <input type="text" id="hospital_affiliation" name="hospital_affiliation" value="{{ old('hospital_affiliation') }}" placeholder="City General Hospital"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('hospital_affiliation') border-red-400 @enderror">
                                    @error('hospital_affiliation')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">Contact Phone</label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 123-4567"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('phone') border-red-400 @enderror">
                                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-1">
                        <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl font-semibold text-sm transition shadow">
                            Create Account
                        </button>
                        <a href="{{ route('signup.role') }}"
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-semibold text-sm text-center transition">
                            Back
                        </a>
                    </div>

                    <p class="text-center text-slate-500 text-sm">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Sign in</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection