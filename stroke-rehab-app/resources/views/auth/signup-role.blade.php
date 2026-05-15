@extends('layouts.app')
@section('hide_nav', true)

@section('content')
<div class="min-h-screen bg-slate-50 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-4xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row min-h-[520px]">

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

            {{-- Right panel --}}
            <div class="md:w-7/12 flex flex-col justify-center px-10 py-12">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-800">Create your account</h2>
                    <p class="text-slate-500 text-sm mt-1">Select whether you're a patient or a healthcare clinician</p>
                </div>

                <div class="space-y-4">
                    <a href="{{ route('signup.patient') }}"
                        class="group flex items-center gap-5 border-2 border-slate-200 hover:border-teal-500 hover:bg-teal-50 rounded-xl p-5 transition-all duration-200">
                        <div class="w-12 h-12 rounded-xl bg-teal-100 group-hover:bg-teal-200 flex items-center justify-center flex-shrink-0 transition-colors">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-slate-800 group-hover:text-teal-700">I'm a Patient</h3>
                            <p class="text-sm text-slate-500 mt-0.5">Track your rehabilitation progress and exercise plans</p>
                        </div>
                        <svg class="w-5 h-5 text-slate-300 group-hover:text-teal-500 ml-auto flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <a href="{{ route('signup.clinician') }}"
                        class="group flex items-center gap-5 border-2 border-slate-200 hover:border-blue-500 hover:bg-blue-50 rounded-xl p-5 transition-all duration-200">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 group-hover:bg-blue-200 flex items-center justify-center flex-shrink-0 transition-colors">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-slate-800 group-hover:text-blue-700">I'm a Clinician</h3>
                            <p class="text-sm text-slate-500 mt-0.5">Create and manage patient rehabilitation plans</p>
                        </div>
                        <svg class="w-5 h-5 text-slate-300 group-hover:text-blue-500 ml-auto flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <p class="text-center text-slate-500 text-sm mt-8">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-teal-600 hover:text-teal-700 font-semibold">Sign in</a>
                </p>

                <p class="text-center text-slate-400 text-xs mt-6">
                    &copy; 2026 Intelligent Post Stroke Patient Rehabilitation Plan Generator
                </p>
            </div>
        </div>
    </div>
</div>
@endsection