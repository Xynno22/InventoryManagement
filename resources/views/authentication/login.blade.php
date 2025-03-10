<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Your App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366F1',
                        bgLight: '#F3F4F6',
                        cardLight: '#FFFFFF',
                        borderLight: '#E5E7EB',
                        textDark: '#1F2937'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-200 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-cardLight text-textDark p-8 rounded-xl shadow-lg border border-borderLight">
        <div class="text-center">
            <h2 class="text-2xl font-bold">Sign in to Your Account</h2>
            <p class="text-gray-600 text-sm mt-1">Enter your credentials to log in</p>
        </div>

        <div class="mt-4">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-100 p-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-md bg-red-100 p-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <form class="mt-4 space-y-4" action="{{ url('/login-company') }}" method="POST">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" id="email" required
                    class="mt-1 block w-full rounded-md bg-white border border-borderLight px-3 py-2 text-gray-900 shadow-sm focus:border-primary focus:ring-primary">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 block w-full rounded-md bg-white border border-borderLight px-3 py-2 text-gray-900 shadow-sm focus:border-primary focus:ring-primary">
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ url('/forgot-password') }}" class="text-sm text-primary hover:underline">
                    Forgot password?
                </a>
            </div>

            <button type="submit"
                class="w-full bg-primary hover:bg-indigo-500 text-white font-semibold py-2 px-4 rounded-md transition">
                Sign in
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-600">
            Don't have an account? 
            <a href="{{ url('/register') }}" class="text-primary hover:underline">Register here</a>
        </p>
    </div>

</body>

</html>
