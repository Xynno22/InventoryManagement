<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm text-center">
        <!-- Logo -->

        <h2 class="text-xl font-semibold text-gray-800 mb-2">Forgot Your Password?</h2>
        <p class="text-gray-600 text-sm mb-6">
            Enter your email address and we will send you instructions to reset your password.
        </p>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <input 
                    type="email" 
                    name="email" 
                    required 
                    placeholder="Email address"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                />
            </div>

            <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-md transition duration-300">
                Continue
            </button>
        </form>

        <a href="/" class="mt-4 block text-blue-600 hover:underline text-sm">
            Back to the Platform
        </a>
    </div>

</body>
</html>
