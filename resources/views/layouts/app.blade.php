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
        <div :class="open ? 'w-64' : 'w-20'"
            class="fixed inset-y-0 left-0 bg-gray-900 text-white transition-all duration-300 ease-in-out flex flex-col justify-between">

            <!-- Menu Atas -->
            <div>
                <div class="flex items-center justify-between px-4 py-4">
                    <h2 :class="open ? 'block' : 'hidden'" class="text-lg font-bold">Menu</h2>
                    <button @click="open = !open" class="text-white text-2xl">
                        <svg x-show="open" xmlns="http://www.w3.org/2000/svg"
                            class="w-8 h-8 text-white hover:text-gray-400 transition" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg"
                            class="w-8 h-8 text-white hover:text-gray-400 transition" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
                <nav class="mt-4">
                    <a href="/dashboard" class="flex items-center px-4 py-3 hover:bg-gray-700 group">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V21H3V9.75z">
                            </path>
                        </svg>
                        <span
                            :class="open ? 'ml-2' :
                                'hidden group-hover:block absolute left-16 bg-gray-800 px-2 py-1 rounded text-sm'">
                            Dashboard
                        </span>
                    </a>

                    <!-- Documents Menu -->
                    <div x-data="{ openDocs: false }">
                        <button @click="openDocs = !openDocs"
                            class="flex items-center justify-between w-full px-4 py-3 hover:bg-gray-700 group">
                            <div class="flex items-center">
                                <svg class="w-6 h-6" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 3h18M3 9h18M3 15h18M3 21h18M6 3v18M12 3v18M18 3v18" />
                                </svg>
                                <span
                                    :class="open ? 'ml-2' :
                                        'hidden group-hover:block absolute left-16 bg-gray-800 px-2 py-1 rounded text-sm'">
                                    Catalog
                                </span>
                            </div>
                            <svg class="w-4 h-4 transition-transform transform"
                                :class="openDocs ? 'rotate-180' : 'rotate-0'" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Submenu -->
                        <div x-show="openDocs" class="ml-6 space-y-2">
                            @if (Auth::guard('company')->check() == true || Auth::user()->can('view category'))
                                <a href="/categories" class="block px-4 py-3 hover:bg-gray-700 flex items-center group">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="white" stroke-width="2"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 4h6v6H4zM4 14h6v6H4zM14 4h6v6h-6zM14 14h6v6h-6z" />
                                    </svg>
                                    <span
                                        :class="open ? 'block' :
                                            'hidden group-hover:block absolute left-20 bg-gray-800 px-2 py-1 rounded text-sm'">
                                        Category
                                    </span>
                                </a>
                            @endif
                            @if (Auth::guard('company')->check() == true || Auth::user()->can('view product'))
                                <a href="/products" class="block px-4 py-3 hover:bg-gray-700 flex items-center group">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="white" stroke-width="2"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7l9-4 9 4v10l-9 4-9-4V7z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9 5 9-5" />
                                    </svg>
                                    <span
                                        :class="open ? 'block' :
                                            'hidden group-hover:block absolute left-20 bg-gray-800 px-2 py-1 rounded text-sm'">
                                        Product
                                    </span>
                                </a>
                            @endif
                        </div>
                    </div>

                    @if (Auth::guard('company')->check() == true)
                        <div x-data="{ openDocs: false }">
                            <button @click="openDocs = !openDocs"
                                class="flex items-center justify-between w-full px-4 py-3 hover:bg-gray-700 group">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6" fill="none" stroke="white" stroke-width="2"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 3h18M3 9h18M3 15h18M3 21h18M6 3v18M12 3v18M18 3v18" />
                                    </svg>
                                    <span
                                        :class="open ? 'ml-2' :
                                            'hidden group-hover:block absolute left-16 bg-gray-800 px-2 py-1 rounded text-sm'">
                                        Manage Admin
                                    </span>
                                </div>
                                <svg class="w-4 h-4 transition-transform transform"
                                    :class="openDocs ? 'rotate-180' : 'rotate-0'" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Submenu -->
                            <div x-show="openDocs" class="ml-6 space-y-2">
                                <a href="/admin" class="block px-4 py-3 hover:bg-gray-700 flex items-center group">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="white" stroke-width="2"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 4h6v6H4zM4 14h6v6H4zM14 4h6v6h-6zM14 14h6v6h-6z" />
                                    </svg>
                                    <span
                                        :class="open ? 'block' :
                                            'hidden group-hover:block absolute left-20 bg-gray-800 px-2 py-1 rounded text-sm'">
                                        Admin
                                    </span>
                                </a>
                                <a href="/roles" class="block px-4 py-3 hover:bg-gray-700 flex items-center group">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="white" stroke-width="2"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7l9-4 9 4v10l-9 4-9-4V7z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9 5 9-5" />
                                    </svg>
                                    <span
                                        :class="open ? 'block' :
                                            'hidden group-hover:block absolute left-20 bg-gray-800 px-2 py-1 rounded text-sm'">
                                        Role
                                    </span>
                                </a>
                            </div>
                        </div>
                    @endif
                </nav>
            </div>
            <div>

                <!-- Menu Bawah -->
                <div class="border-t border-gray-700">
                    @if (Auth::guard('company')->check() == true)
                    <!-- Profile -->
                    <a href="{{ route('profile.index') }}"
                        class="relative flex items-center px-4 py-2 hover:bg-gray-700 group">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z">
                            </path>
                        </svg>
                        <span
                            :class="open ? 'ml-2' :
                                'hidden group-hover:block absolute left-full ml-2 bg-gray-800 px-2 py-1 rounded text-sm text-white'">
                            Profile
                        </span>
                    </a>

                    <!-- Delete Account -->
                    <form id="delete-account-form" action="{{ route('company.destroy') }}" method="POST"
                        class="relative">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete()"
                            class="flex items-center px-4 py-2 text-white rounded-lg hover:bg-red-700 w-full text-left group">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span
                                :class="open ? 'ml-2' :
                                    'hidden group-hover:block absolute left-full ml-2 bg-gray-800 px-2 py-1 rounded text-sm text-white'">
                                Delete Account
                            </span>
                        </button>
                    </form>
                @endif

                    <!-- Logout -->
                    @if (Auth::guard('company')->check() == true)
                        <form action="{{ route('logout') }}" method="POST" class="relative">
                            @csrf
                            <button type="submit"
                                class="flex items-center px-4 py-2 hover:bg-gray-700 w-full text-left group">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m10 4v4m0-4V7">
                                    </path>
                                </svg>
                                <span
                                    :class="open ? 'ml-2' :
                                        'hidden group-hover:block absolute left-full ml-2 bg-gray-800 px-2 py-1 rounded text-sm text-white'">
                                    Logout
                                </span>
                            </button>
                        </form>
                    @endif
                    @if (Auth::guard('web')->check() == true)
                        <form action="{{ route('logoutUser') }}" method="POST" class="relative">
                            @csrf
                            <button type="submit"
                                class="flex items-center px-4 py-2 hover:bg-gray-700 w-full text-left group">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m10 4v4m0-4V7">
                                    </path>
                                </svg>
                                <span
                                    :class="open ? 'ml-2' :
                                        'hidden group-hover:block absolute left-full ml-2 bg-gray-800 px-2 py-1 rounded text-sm text-white'">
                                    Logout
                                </span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
        <!-- Main Content -->
        <div :class="open ? 'ml-64' : 'ml-20'"
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
