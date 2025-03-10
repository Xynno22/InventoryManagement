<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Tambahkan SweetAlert -->
</head>

<body class="bg-gray-100">
    <div x-data="{ open: true }" class="flex h-screen">

        <!-- Sidebar -->
        <div :class="open ? 'w-64' : 'w-16'"
            class="fixed inset-y-0 left-0 bg-gray-900 text-white transition-all duration-300 ease-in-out flex flex-col justify-between">

            <!-- Menu Atas -->
            <div>
                <div class="flex items-center justify-between px-4 py-4">
                    <h2 :class="open ? 'block' : 'hidden'" class="text-lg font-bold">Main Menu</h2>
                    <button @click="open = !open" class="text-white text-2xl">
                        <template x-if="open">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-6 h-6 text-white hover:text-gray-400 transition" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </template>
                        <template x-if="!open">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-6 h-6 text-white hover:text-gray-400 transition" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"></path>
                            </svg>
                        </template>
                    </button>
                </div>
                <nav class="mt-4">
                    <a href="/dashboard" class="flex items-center px-4 py-2 hover:bg-gray-700">
                        <!-- Ikon Dashboard -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V21H3V9.75z">
                            </path>
                        </svg>
                        <span :class="open ? 'ml-2' : 'hidden'">Dashboard</span>
                    </a>
                    <a href="/documents" class="flex items-center px-4 py-2 hover:bg-gray-700">
                        <!-- Ikon Documents -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 16h8M8 12h8m-8-4h4m8 12H4V4h10l6 6v10z"></path>
                        </svg>
                        <span :class="open ? 'ml-2' : 'hidden'">Documents</span>
                    </a>
                </nav>
            </div>

            <!-- Menu Bawah -->
            <div class="border-t border-gray-700">
                <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-2 hover:bg-gray-700">
                    <!-- Ikon Profile -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z">
                        </path>
                    </svg>
                    <span :class="open ? 'ml-2' : 'hidden'">Profile</span>
                </a>

                <!-- Delete Account Button with Confirmation -->
                <form id="delete-account-form" action="{{ route('company.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete()" class="flex items-center px-4 py-2 text-white rounded-lg hover:bg-red-700 w-full text-left">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="ml-2">Delete Account</span>
                    </button>
                </form>

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center px-4 py-2 hover:bg-gray-700 w-full text-left">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m10 4v4m0-4V7">
                            </path>
                        </svg>
                        <span class="ml-2">Logout</span>
                    </button>
                </form>                
            </div>
        </div>

        <!-- Main Content -->
        <div :class="open ? 'ml-64' : 'ml-16'"
            class="flex-1 flex flex-col transition-all duration-300 bg-gray-100 text-gray-900">
            <header class="bg-gray-800 text-white shadow p-4 flex justify-between items-center">
                <h1 class="text-xl font-bold">@yield('title')</h1>
            </header>

            <main class="p-6">
                @yield('content')
            </main>
        </div>

    </div>

    <!-- JavaScript untuk Konfirmasi Delete -->
    <script>
        function confirmDelete() {
            Swal.fire({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this account!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, Delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-account-form').submit();
                }
            });
        }
    </script>
</body>

</html>
