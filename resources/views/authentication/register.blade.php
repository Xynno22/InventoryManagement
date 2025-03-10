<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Your App</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <h2 class="text-2xl font-bold">Register Your Company</h2>
            <p class="text-gray-600 text-sm mt-1">Create an account to get started</p>
        </div>

        <div class="mt-4">
            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-100 p-3 text-sm text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <form class="mt-4 space-y-4" action="{{ url('/register-company') }}" method="POST">
            @csrf

            <!-- Company Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Company Name</label>
                <input type="text" name="name" id="name" required
                    class="mt-1 block w-full rounded-md bg-white border border-borderLight px-3 py-2 text-gray-900 shadow-sm focus:border-primary focus:ring-primary">
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" id="email" required
                    class="mt-1 block w-full rounded-md bg-white border border-borderLight px-3 py-2 text-gray-900 shadow-sm focus:border-primary focus:ring-primary">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 block w-full rounded-md bg-white border border-borderLight px-3 py-2 text-gray-900 shadow-sm focus:border-primary focus:ring-primary">
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="mt-1 block w-full rounded-md bg-white border border-borderLight px-3 py-2 text-gray-900 shadow-sm focus:border-primary focus:ring-primary">
            </div>

            <button type="submit"
                class="w-full bg-primary hover:bg-indigo-500 text-white font-semibold py-2 px-4 rounded-md transition">
                Register Company
            </button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-600">
            Already have an account?
            <a href="{{ url('/login') }}" class="text-primary hover:underline">Login here</a>
        </p>
    </div>

</body>

</html>
