<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stroke Rehabilitation - @yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    @unless(View::hasSection('hide_nav'))
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-blue-600">StrokeRehab</a>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                    <span class="text-gray-700">{{ auth()->user()->name }}</span>
                    <span class="text-gray-500 text-sm">({{ ucfirst(auth()->user()->role) }})</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Logout</button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    @endunless

    <main>
        @yield('content')
    </main>

    @unless(View::hasSection('hide_nav'))
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <p class="text-center text-gray-600 text-sm">
                &copy; 2026 Intelligent Post Stroke Patient Rehabilitation Plan Generator. All rights reserved.
            </p>
        </div>
    </footer>
    @endunless
</body>

</html>