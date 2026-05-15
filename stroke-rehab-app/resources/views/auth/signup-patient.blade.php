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
                <div class="mt-2 space-y-3 w-full">
                    <div class="flex items-center gap-3 text-sm text-slate-300">
                        <div class="w-8 h-8 rounded-full bg-teal-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span>Personalised rehabilitation plans</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-slate-300">
                        <div class="w-8 h-8 rounded-full bg-teal-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span>Track exercise progress daily</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-slate-300">
                        <div class="w-8 h-8 rounded-full bg-teal-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.768-.231-1.48-.634-2.073M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.768.231-1.48.634-2.073M9 12a4 4 0 108 0 4 4 0 00-8 0z" />
                            </svg>
                        </div>
                        <span>Clinician &amp; patient collaboration</span>
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
                    <h2 class="text-2xl font-bold text-slate-800">Patient Sign Up</h2>
                    <p class="text-slate-500 text-sm mt-1">Create your account and start tracking your rehabilitation journey</p>
                </div>

                <form method="POST" action="{{ route('register.patient') }}" class="space-y-5">
                    @csrf

                    {{-- Account Information --}}
                    <div class="border-b border-slate-100 pb-5">
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-4">Account Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Full Name *</label>
                                <input type="text" id="name" name="name" required value="{{ old('name') }}" placeholder="John Doe"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('name') border-red-400 @enderror">
                                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address *</label>
                                <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="john@example.com"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('email') border-red-400 @enderror">
                                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password *</label>
                                    <input type="password" id="password" name="password" required placeholder="••••••••"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('password') border-red-400 @enderror">
                                    <p class="text-slate-400 text-xs mt-1">Minimum 8 characters</p>
                                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm Password *</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Clinical Information --}}
                    <div class="border-b border-slate-100 pb-5">
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-4">Clinical Information</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="age" class="block text-sm font-medium text-slate-700 mb-1.5">Age *</label>
                                    <input type="number" id="age" name="age" required min="0" max="150" value="{{ old('age') }}" placeholder="65"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('age') border-red-400 @enderror">
                                    @error('age')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="gender" class="block text-sm font-medium text-slate-700 mb-1.5">Gender *</label>
                                    <select id="gender" name="gender" required
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('gender') border-red-400 @enderror">
                                        <option value="">Select gender</option>
                                        <option value="0" @if(old('gender')=='0' ) selected @endif>Female</option>
                                        <option value="1" @if(old('gender')=='1' ) selected @endif>Male</option>
                                    </select>
                                    @error('gender')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="rsbp" class="block text-sm font-medium text-slate-700 mb-1.5">Systolic BP (mmHg)</label>
                                    <input type="number" id="rsbp" name="rsbp" min="0" max="300" value="{{ old('rsbp') }}" placeholder="120"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('rsbp') border-red-400 @enderror">
                                    @error('rsbp')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="stroke_subtype" class="block text-sm font-medium text-slate-700 mb-1.5">Stroke Type</label>
                                    <select id="stroke_subtype" name="stroke_subtype"
                                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('stroke_subtype') border-red-400 @enderror">
                                        <option value="">Select stroke type</option>
                                        <option value="TACS" @if(old('stroke_subtype')=='TACS' ) selected @endif>TACS (Total Anterior)</option>
                                        <option value="PACS" @if(old('stroke_subtype')=='PACS' ) selected @endif>PACS (Partial Anterior)</option>
                                        <option value="LACS" @if(old('stroke_subtype')=='LACS' ) selected @endif>LACS (Lacunar)</option>
                                        <option value="POCS" @if(old('stroke_subtype')=='POCS' ) selected @endif>POCS (Posterior)</option>
                                        <option value="OTH" @if(old('stroke_subtype')=='OTH' ) selected @endif>OTH (Other)</option>
                                    </select>
                                    @error('stroke_subtype')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div>
                                <label for="conscious_state" class="block text-sm font-medium text-slate-700 mb-1.5">Consciousness State</label>
                                <select id="conscious_state" name="conscious_state"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 @error('conscious_state') border-red-400 @enderror">
                                    <option value="">Select consciousness state</option>
                                    <option value="Alert" @if(old('conscious_state')=='Alert' ) selected @endif>Alert</option>
                                    <option value="Drowsy" @if(old('conscious_state')=='Drowsy' ) selected @endif>Drowsy</option>
                                    <option value="Unconscious" @if(old('conscious_state')=='Unconscious' ) selected @endif>Unconscious</option>
                                </select>
                                @error('conscious_state')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Functional Deficits --}}
                    <div class="pb-3">
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Functional Deficits</h3>
                        <p class="text-slate-500 text-xs mb-4">Select any deficits you currently experience</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach([
                            ['rdef1','Face Deficit'],['rdef2','Arm/Hand Deficit'],
                            ['rdef3','Leg/Foot Deficit'],['rdef4','Speech Deficit'],
                            ['rdef5','Vision Deficit'],['rdef6','Visuospatial Deficit'],
                            ['rdef7','Brainstem/Cerebellar'],['rdef8','Other Deficits']
                            ] as [$field, $label])
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="{{ $field }}" value="1"
                                    class="rounded border-slate-300 text-teal-600 focus:ring-teal-400"
                                    @if(old($field)) checked @endif>
                                <span class="text-sm text-slate-700">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex gap-3 pt-1">
                        <button type="submit"
                            class="flex-1 bg-teal-600 hover:bg-teal-700 text-white py-2.5 rounded-xl font-semibold text-sm transition shadow">
                            Create Account
                        </button>
                        <a href="{{ route('signup.role') }}"
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-xl font-semibold text-sm text-center transition">
                            Back
                        </a>
                    </div>

                    <p class="text-center text-slate-500 text-sm">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-teal-600 hover:text-teal-700 font-semibold">Sign in</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection