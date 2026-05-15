<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stroke Rehabilitation - @yield('title', 'Clinician Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-b from-sky-500 to-sky-600 text-white shadow-lg flex flex-col">
            <div class="p-6 border-b border-sky-300">
                <h1 class="text-2xl font-bold">StrokeRehab</h1>
                <p class="text-sky-200 text-sm mt-1">Clinician Portal</p>
            </div>

            <!-- User Info -->
            <div class="p-6 border-b border-sky-300">
                <p class="text-sm text-sky-200">Logged in as</p>
                <p class="font-semibold text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs text-sky-300 mt-1">{{ auth()->user()->email }}</p>
            </div>

            <!-- Navigation Menu -->
            <nav class="mt-6 flex-1 overflow-y-auto">
                <div class="px-4 py-2">
                    <a href="{{ route('clinician.dashboard') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                       @if(request()->routeIs('clinician.dashboard')) bg-sky-700 text-white @else text-sky-100 hover:bg-sky-700 @endif">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="px-4 py-2">
                    <a href="{{ route('clinician.patients.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                       @if(request()->routeIs('clinician.patients.*')) bg-sky-700 text-white @else text-sky-100 hover:bg-sky-700 @endif">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                        </svg>
                        <span>My Patients</span>
                    </a>
                </div>

                <div class="px-4 py-2">
                    <a href="{{ route('clinician.feedback.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                       @if(request()->routeIs('clinician.feedback.*')) bg-sky-700 text-white @else text-sky-100 hover:bg-sky-700 @endif">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        <span>Patient Feedback</span>
                    </a>
                </div>

            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-sky-300">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sky-100 hover:bg-sky-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <div class="bg-white border-b border-gray-200 shadow-sm">
                <div class="px-8 py-4 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-900">@yield('page_title', 'Dashboard')</h2>
                    <div class="flex items-center gap-4">
                        <span class="text-gray-600">{{ auth()->user()->name }}</span>
                        <a href="{{ route('clinician.profile.show') }}" class="px-4 py-2 rounded-lg text-sky-600 hover:bg-sky-100 transition font-medium @if(request()->routeIs('clinician.profile.*')) bg-sky-100 text-sky-700 @endif">Profile</a>
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