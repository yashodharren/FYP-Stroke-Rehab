<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stroke Rehabilitation - @yield('title', 'Patient Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-b from-green-700 to-green-900 text-white shadow-lg flex flex-col">
            <div class="p-6 border-b border-green-600">
                <h1 class="text-2xl font-bold">StrokeRehab</h1>
                <p class="text-green-200 text-sm mt-1">Patient Portal</p>
            </div>

            <!-- User Info -->
            <div class="p-6 border-b border-green-600">
                <p class="text-sm text-green-200">Logged in as</p>
                <p class="font-semibold text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs text-green-300 mt-1">{{ auth()->user()->email }}</p>
            </div>

            <!-- Navigation Menu -->
            <nav class="mt-6 flex-1 overflow-y-auto">
                <div class="px-4 py-2">
                    <a href="{{ route('patient.dashboard') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                       @if(request()->routeIs('patient.dashboard')) bg-green-600 text-white @else text-green-100 hover:bg-green-600 @endif">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="px-4 py-2">
                    <a href="{{ route('patient.schedule') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                       @if(request()->routeIs('patient.schedule')) bg-green-600 text-white @else text-green-100 hover:bg-green-600 @endif">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>My Schedule</span>
                    </a>
                </div>

                <div class="px-4 py-2">
                    <a href="{{ route('patient.progress') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                       @if(request()->routeIs('patient.progress')) bg-green-600 text-white @else text-green-100 hover:bg-green-600 @endif">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>My Progress</span>
                    </a>
                </div>

                <div class="px-4 py-2">
                    <a href="{{ route('patient.feedback-form') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                       @if(request()->routeIs('patient.feedback-form')) bg-green-600 text-white @else text-green-100 hover:bg-green-600 @endif">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Submit Feedback</span>
                    </a>
                </div>

            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-green-600">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-green-100 hover:bg-green-600 transition">
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
                        <a href="{{ route('patient.profile.show') }}" class="px-4 py-2 rounded-lg text-green-600 hover:bg-green-100 transition font-medium @if(request()->routeIs('patient.profile.*')) bg-green-100 text-green-700 @endif">Profile</a>
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