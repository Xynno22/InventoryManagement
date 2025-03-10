<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password - Your App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="w-full max-w-md bg-white rounded-lg shadow-md p-6 space-y-6">
    <h2 class="text-2xl font-bold text-center text-gray-900">Reset Your Password</h2>

    @if (session('error'))
        <div class="p-3 text-sm text-red-700 bg-red-100 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
            <input type="password" name="password" id="password" required 
                   class="mt-1 block w-full rounded-md border border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm px-3 py-2">
            @error('password')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
    
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required 
                   class="mt-1 block w-full rounded-md border border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm px-3 py-2">
        </div>
    
        <div class="flex justify-end">
            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Reset Password
            </button>
        </div>
    </form>
    

</div>

</body>
</html>

