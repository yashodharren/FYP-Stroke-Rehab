<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Stroke Rehabilitation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-blue-600">StrokeRehab</h1>
                <p class="text-gray-600 mt-2">Intelligent Rehabilitation Plan Generator</p>
            </div>

            @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                <p class="font-semibold">Login Failed</p>
                <p class="text-sm mt-1">{{ $errors->first('email') }}</p>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                        placeholder="you@example.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="••••••••">
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-medium transition">
                    Sign In
                </button>
            </form>

            <div class="mt-8 border-t border-gray-200 pt-6">
                <p class="text-center text-gray-600 text-sm mb-4">
                    Don't have an account?
                    <a href="{{ route('signup.role') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Sign up here</a>
                </p>
            </div>

            <div class="mt-6 border-t border-gray-200 pt-6">
                <p class="text-center text-gray-600 text-sm mb-4">Demo Credentials:</p>
                <div class="space-y-3 text-sm">
                    <div class="bg-blue-50 p-3 rounded">
                        <p class="font-semibold text-blue-900">Admin</p>
                        <p class="text-blue-700">admin@rehab.local</p>
                        <p class="text-blue-700">password</p>
                    </div>
                    <div class="bg-green-50 p-3 rounded">
                        <p class="font-semibold text-green-900">Clinician</p>
                        <p class="text-green-700">clinician@rehab.local</p>
                        <p class="text-green-700">password</p>
                    </div>
                    <div class="bg-purple-50 p-3 rounded">
                        <p class="font-semibold text-purple-900">Patient</p>
                        <p class="text-purple-700">patient1@rehab.local</p>
                        <p class="text-purple-700">password</p>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-center text-gray-600 text-sm mt-6">
            &copy; 2026 Stroke Rehabilitation System. All rights reserved.
        </p>
    </div>
</body>

</html>