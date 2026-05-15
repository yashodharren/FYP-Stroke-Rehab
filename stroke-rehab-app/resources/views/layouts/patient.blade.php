<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stroke Rehabilitation - @yield('title', 'Patient Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-slate-900 text-white shadow-xl flex flex-col">

            <!-- Brand -->
            <div class="px-5 py-4 border-b border-slate-600">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="StrokeRehab Logo" class="w-16 h-16 object-contain flex-shrink-0">
                    <div>
                        <h1 class="text-base font-bold leading-tight tracking-tight">StrokeRehab</h1>
                        <p class="text-xs text-slate-400 leading-tight">Patient Portal</p>
                    </div>
                </div>
            </div>

            <!-- User Info -->
            <div class="px-6 py-4 border-b border-slate-600">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-teal-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="mt-4 flex-1 overflow-y-auto px-3 space-y-1">
                <a href="{{ route('patient.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition font-medium
                    @if(request()->routeIs('patient.dashboard')) bg-teal-600 text-white @else text-slate-300 hover:bg-slate-800 hover:text-white @endif">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('patient.schedule') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition font-medium
                    @if(request()->routeIs('patient.schedule')) bg-teal-600 text-white @else text-slate-300 hover:bg-slate-800 hover:text-white @endif">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>My Schedule</span>
                </a>

                <a href="{{ route('patient.progress') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition font-medium
                    @if(request()->routeIs('patient.progress')) bg-teal-600 text-white @else text-slate-300 hover:bg-slate-800 hover:text-white @endif">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>My Progress</span>
                </a>

                <a href="{{ route('patient.feedback-form') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition font-medium
                    @if(request()->routeIs('patient.feedback-form')) bg-teal-600 text-white @else text-slate-300 hover:bg-slate-800 hover:text-white @endif">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    <span>Submit Feedback</span>
                </a>
            </nav>

            <!-- Logout -->
            <div class="p-3 border-t border-slate-500">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-200 hover:bg-slate-800 hover:text-white transition font-medium">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <div class="bg-slate-900 border-b border-gray-200 border-l border-l-slate-600 shadow-sm">
                <div class="px-8 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-slate-800">@yield('page_title', 'Dashboard')</h2>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400">Patient</p>
                        </div>
                        <a href="{{ route('patient.profile.show') }}"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium border transition
                           @if(request()->routeIs('patient.profile.*')) bg-teal-50 text-teal-700 border-teal-200 @else bg-teal-600 text-slate-100 border-black hover:bg-teal-500 @endif">
                            Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="flex-1 overflow-auto">
                <div class="p-8">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

    <script>
        function showToast(message, type) {
            const colors = {
                success: {
                    bg: 'bg-green-500',
                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                },
                error: {
                    bg: 'bg-red-500',
                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
                },
                warning: {
                    bg: 'bg-yellow-500',
                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>'
                },
                info: {
                    bg: 'bg-blue-500',
                    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                },
            };
            const c = colors[type] || colors.info;
            const toast = document.createElement('div');
            toast.className = `pointer-events-auto flex items-start gap-3 ${c.bg} text-white px-4 py-3 rounded-xl shadow-lg min-w-[280px] max-w-sm translate-x-24 opacity-0 transition-all duration-300`;
            toast.innerHTML = `
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">${c.icon}</svg>
            <span class="text-sm font-medium flex-1 leading-snug">${message}</span>
            <button onclick="dismissToast(this.parentElement)" class="ml-1 opacity-70 hover:opacity-100 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>`;
            document.getElementById('toast-container').appendChild(toast);
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    toast.classList.remove('translate-x-24', 'opacity-0');
                });
            });
            setTimeout(() => dismissToast(toast), 5000);
        }

        function dismissToast(toast) {
            toast.classList.add('translate-x-24', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }
        @if(session('success')) showToast(@json(session('success')), 'success');
        @endif
        @if(session('error')) showToast(@json(session('error')), 'error');
        @endif
        @if(session('warning')) showToast(@json(session('warning')), 'warning');
        @endif
        @if(session('info')) showToast(@json(session('info')), 'info');
        @endif
    </script>

</body>

</html>