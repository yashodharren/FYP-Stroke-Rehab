<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Stroke Rehabilitation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-4xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row min-h-[520px]">

            {{-- Left branding panel --}}
            <div class="bg-slate-900 text-white md:w-5/12 flex flex-col items-center justify-center px-10 py-12 gap-6">
                <img src="{{ asset('images/logo.png') }}" alt="StrokeRehab Logo" class="w-44 h-44 object-contain">
                <div class="text-center">
                    <h1 class="text-2xl font-bold tracking-tight">StrokeRehab</h1>
                    <p class="text-slate-400 text-sm mt-2 leading-relaxed">Intelligent Post-Stroke<br>Rehabilitation Plan Generator</p>
                </div>
                <div class="mt-4 space-y-3 w-full">
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
            <div class="md:w-7/12 flex flex-col justify-center px-10 py-12">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-800">Welcome back</h2>
                    <p class="text-slate-500 text-sm mt-1">Sign in to continue to your portal</p>
                </div>

                @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                    <div>
                        <p class="font-semibold text-sm">Login Failed</p>
                        <p class="text-sm mt-0.5">{{ $errors->first('email') }}</p>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent @error('email') border-red-400 @enderror"
                                placeholder="you@example.com">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-teal-600 hover:bg-teal-700 text-white py-2.5 rounded-xl font-semibold text-sm transition shadow">
                        Sign In
                    </button>
                </form>

                <p class="text-center text-slate-500 text-sm mt-6">
                    Don't have an account?
                    <a href="{{ route('signup.role') }}" class="text-teal-600 hover:text-teal-700 font-semibold">Sign up here</a>
                </p>

                <p class="text-center text-slate-400 text-xs mt-8">
                    &copy; 2026 Intelligent Post Stroke Patient Rehabilitation Plan Generator
                </p>
            </div>
        </div>
    </div>
</body>

</html>