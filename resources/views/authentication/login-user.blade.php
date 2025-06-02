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
            <h2 class="text-2xl font-bold">Login as Co-Worker</h2>
            <p class="text-gray-600 text-sm mt-1">Enter your credentials to log in</p>
        </div>

        <div class="flex justify-center space-x-4 mb-4">
            <a href="{{ url('/login') }}" class="text-primary font-semibold hover:underline">
                Login as Company
            </a>
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

        <form class="mt-4 space-y-4" action="{{ url('/login-user') }}" method="POST">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email" id="email" required
                    class="mt-1 block w-full rounded-md bg-white border border-borderLight px-3 py-2 text-gray-900 shadow-sm focus:border-primary focus:ring-primary focus:outline-none">
            </div>

            <div x-data="{ showPassword: false }">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="relative mt-1">
                    <input x-bind:type="showPassword ? 'text' : 'password'" name="password" id="password" required
                        class="block w-full rounded-md bg-white border border-borderLight px-3 py-2 pr-10 text-gray-900 shadow-sm focus:border-primary focus:ring-primary focus:outline-none">
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors duration-200">
                        <!-- Eye Icon (visible when password is hidden) -->
                        <svg x-show="!showPassword" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                        <!-- Eye Slash Icon (visible when password is shown) -->
                        <svg x-show="showPassword" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M6.71277 6.7226C3.66479 8.79527 2 12 2 12C2 12 5.63636 19 12 19C14.0503 19 15.8174 18.2734 17.2711 17.2884M11 5.05822C11.3254 5.02013 11.6588 5 12 5C18.3636 5 22 12 22 12C22 12 21.3082 13.3317 20 14.8335"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path
                                d="M14 14.2362C13.4692 14.7112 12.7684 15.0001 12 15.0001C10.3431 15.0001 9 13.657 9 12.0001C9 11.1764 9.33193 10.4303 9.86932 9.88818"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-primary hover:bg-indigo-500 text-white font-semibold py-2 px-4 rounded-md transition">
                Sign in
            </button>
        </form>
    </div>

</body>

</html>
