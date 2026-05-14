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