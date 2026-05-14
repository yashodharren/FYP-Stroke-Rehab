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
                    <a href="{{ route('patient.details') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                       @if(request()->routeIs('patient.details')) bg-green-600 text-white @else text-green-100 hover:bg-green-600 @endif">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>My Details</span>
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
                    @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</body>

</html>