<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.8.4/axios.min.js"
        integrity="sha512-2A1+/TAny5loNGk3RBbk11FwoKXYOMfAK6R7r4CpQH7Luz4pezqEGcfphoNzB7SM4dixUoJsKkBsB6kg+dNE2g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Tambahkan SweetAlert -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Neucha&family=Red+Hat+Display:ital,wght@0,300..900;1,300..900&family=Space+Grotesk&display=swap"
        rel="stylesheet">
    <style>
        .menu::-webkit-scrollbar {
            display: none;
            /* Untuk Chrome, Safari, dan Edge */
        }
    </style>
</head>


<body class="bg-gray-100">
    <div x-data="{ open: true }" class="flex h-screen">
        <!-- Sidebar -->
        <div :class="open ? 'w-64' : 'w-20'"
            class="menu fixed inset-y-0 left-0 bg-gray-900 text-white transition-all duration-300 ease-in-out flex flex-col justify-between overflow-y-auto">

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
                    <a href="/dashboard"
                        class="flex items-center px-4 py-3 hover:bg-gray-700 group {{ Request::is('dashboard') ? 'bg-gray-700' : '' }}">
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
                    @if (Auth::guard('company')->check() == true ||
                            Auth::user()->can('view category') ||
                            Auth::user()->can('view product') ||
                            Auth::user()->can('view promo'))
                        <div x-data="{ openDocs: {{ Request::is('categories*', 'products*', 'promo*') ? 'true' : 'false' }} }">
                            <button @click="openDocs = !openDocs"
                                class="flex items-center justify-between w-full px-4 py-3 hover:bg-gray-700 group">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6" fill="none" stroke="white" stroke-width="2"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 7L12 2L21 7V17L12 22L3 17V7Z" stroke="#FFFFFF" stroke-width="2"
                                            fill="none" />
                                        <path d="M12 22V12L21 7" stroke="#FFFFFF" stroke-width="2" fill="none" />
                                        <path d="M12 12L3 7" stroke="#FFFFFF" stroke-width="2" fill="none" />
                                    </svg>
                                    <span
                                        :class="open ? 'ml-2' :
                                            'hidden group-hover:block absolute left-16 bg-gray-800 px-2 py-1 rounded text-sm'">
                                        Product Management
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
                                    <a href="/categories"
                                        class="block px-4 py-3 hover:bg-gray-700 flex items-center group {{ Request::is('categories*') ? 'bg-gray-700' : '' }}">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="white" stroke-width="2"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4 4h6v6H4zM4 14h6v6H4zM14 4h6v6h-6zM14 14h6v6h-6z" />
                                        </svg>
                                        <span
                                            :class="open ? 'block' :
                                                'hidden group-hover:block absolute left-20 bg-gray-800 px-2 py-1 rounded text-sm'">
                                            Product Category
                                        </span>
                                    </a>
                                @endif
                                @if (Auth::guard('company')->check() == true || Auth::user()->can('view product'))
                                    <a href="/products"
                                        class="block px-4 py-3 hover:bg-gray-700 flex items-center group {{ Request::is('products*') ? 'bg-gray-700' : '' }}">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="white" stroke-width="2"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 7l9-4 9 4v10l-9 4-9-4V7z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9 5 9-5" />
                                        </svg>
                                        <span
                                            :class="open ? 'block' :
                                                'hidden group-hover:block absolute left-20 bg-gray-800 px-2 py-1 rounded text-sm'">
                                            Product List
                                        </span>
                                    </a>
                                @endif
                                @if (Auth::guard('company')->check() == true || Auth::user()->can('view promo'))
                                    <a href="/promo"
                                        class="block px-4 py-3 hover:bg-gray-700 flex items-center group {{ Request::is('promo*') ? 'bg-gray-700' : '' }}">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="white" stroke-width="2"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m8.99 14.993 6-6m6 3.001c0 1.268-.63 2.39-1.593 3.069a3.746 3.746 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043 3.745 3.745 0 0 1-3.068 1.593c-1.268 0-2.39-.63-3.068-1.593a3.745 3.745 0 0 1-3.296-1.043 3.746 3.746 0 0 1-1.043-3.297 3.746 3.746 0 0 1-1.593-3.068c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 0 1 1.043-3.297 3.745 3.745 0 0 1 3.296-1.042 3.745 3.745 0 0 1 3.068-1.594c1.268 0 2.39.63 3.068 1.593a3.745 3.745 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.297 3.746 3.746 0 0 1 1.593 3.068ZM9.74 9.743h.008v.007H9.74v-.007Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                        <span
                                            :class="open ? 'block' :
                                                'hidden group-hover:block absolute left-20 bg-gray-800 px-2 py-1 rounded text-sm'">
                                            Promo List
                                        </span>
                                    </a>
                                @endif

                            </div>
                        </div>
                    @endif
                    @if (Auth::guard('company')->check() == true ||
                            Auth::user()->can('view stock') ||
                            Auth::user()->can('view stock opname'))
                        <div x-data="{ openDocs: {{ Request::is('stock*', 'opname*') ? 'true' : 'false' }} }">
                            <button @click="openDocs = !openDocs"
                                class="flex items-center justify-between w-full px-4 py-3 hover:bg-gray-700 group">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6" fill="none" stroke="white" stroke-width="2"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="3" y="5" width="18" height="14" rx="2"
                                            stroke="#FFFFFF" stroke-width="2" fill="none" />
                                        <path d="M3 10H21" stroke="#FFFFFF" stroke-width="2"
                                            stroke-linecap="round" />
                                        <rect x="7" y="12" width="3" height="4" fill="#FFFFFF" />
                                        <rect x="14" y="12" width="3" height="4" fill="#FFFFFF" />
                                    </svg>
                                    <span
                                        :class="open ? 'ml-2' :
                                            'hidden group-hover:block absolute left-16 bg-gray-800 px-2 py-1 rounded text-sm'">
                                        Stock Management
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

                                @if (Auth::guard('company')->check() == true || Auth::user()->can('view stock'))
                                    <a href="/stocks"
                                        class="block px-4 py-3 hover:bg-gray-700 flex items-center group {{ Request::is('stock*') ? 'bg-gray-700' : '' }}">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="white" stroke-width="2"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="7" stroke="#FFFFFF"
                                                stroke-width="2" fill="none" />
                                            <path d="M12 5V8M12 16V19M12 11H9M12 11H15" stroke="#FFFFFF"
                                                stroke-width="2" stroke-linecap="round" />
                                        </svg>
                                        <span
                                            :class="open ? 'block' :
                                                'hidden group-hover:block absolute left-20 bg-gray-800 px-2 py-1 rounded text-sm'">
                                            Stock
                                        </span>
                                    </a>
                                @endif
                                @if (Auth::guard('company')->check() == true || Auth::user()->can('view stock opname'))
                                    <a href="/opname"
                                        class="block px-4 py-3 hover:bg-gray-700 flex items-center group {{ Request::is('opname*') ? 'bg-gray-700' : '' }}">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5h6m2 2H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V9a2 2 0 00-2-2z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 14h.01M9 17h.01M12 14h3m-3 3h3" />
                                        </svg>
                                        <span
                                            :class="open ? 'block' :
                                                'hidden group-hover:block absolute left-20 bg-gray-800 px-2 py-1 rounded text-sm'">
                                            Stock Opname
                                        </span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if (Auth::guard('company')->check() == true || Auth::user()->can('view transaction'))
                        <div x-data="{ openDocs: {{ Request::is('transaction*', 'operational*') ? 'true' : 'false' }} }">
                            <button @click="openDocs = !openDocs"
                                class="flex items-center justify-between w-full px-4 py-3 hover:bg-gray-700 group">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 12h18M3 6h18M3 18h18" />
                                        <path d="M12 3v18" />
                                    </svg>
                                    <span
                                        :class="open ? 'ml-2' :
                                            'hidden group-hover:block absolute left-16 bg-gray-800 px-2 py-1 rounded text-sm'">
                                        Finance & Cost
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

                                @if (Auth::guard('company')->check() == true || Auth::user()->can('view transaction'))
                                    <a href="/transaction"
                                        class="block px-4 py-3 hover:bg-gray-700 flex items-center group {{ Request::is('transaction*') ? 'bg-gray-700' : '' }}">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m9 14.25 6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                        <span
                                            :class="open ? 'block' :
                                                'hidden group-hover:block absolute left-20 bg-gray-800 px-2 py-1 rounded text-sm'">
                                            Transaction
                                        </span>
                                    </a>
                                @endif
                            </div>
                            <div x-show="openDocs" class="ml-6 space-y-2">

                                @if (Auth::guard('company')->check() == true || Auth::user()->can('view operational expenses'))
                                    <a href="/operational"
                                        class="block px-4 py-3 hover:bg-gray-700 flex items-center group {{ Request::is('operational*') ? 'bg-gray-700' : '' }}">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                        </svg>
                                        <span
                                            :class="open ? 'block' :
                                                'hidden group-hover:block absolute left-20 bg-gray-800 px-2 py-1 rounded text-sm'">
                                            Operational Expenses
                                        </span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if (Auth::guard('company')->check() == true)
                        <div x-data="{ openDocs: false }">
                            <button @click="openDocs = !openDocs"
                                class="flex items-center justify-between w-full px-4 py-3 hover:bg-gray-700 group">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6" fill="none" stroke="white" stroke-width="2"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="7" r="4" stroke="#FFFFFF"
                                            stroke-width="2" fill="none" />
                                        <path d="M4 17C4 14.79 5.79 13 8 13H16C18.21 13 20 14.79 20 17"
                                            stroke="#FFFFFF" stroke-width="2" />
                                        <path d="M8 13V15C8 15.55 8.45 16 9 16H15C15.55 16 16 15.55 16 15V13"
                                            stroke="#FFFFFF" stroke-width="2" />
                                    </svg>
                                    <span
                                        :class="open ? 'ml-2' :
                                            'hidden group-hover:block absolute left-16 bg-gray-800 px-2 py-1 rounded text-sm'">
                                        Admin Management
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
                                        <circle cx="12" cy="7" r="4" stroke="#FFFFFF"
                                            stroke-width="2" fill="none" />
                                        <path d="M16 17C16 14.79 14.21 13 12 13C9.79 13 8 14.79 8 17" stroke="#FFFFFF"
                                            stroke-width="2" />
                                        <path d="M12 17L12 20" stroke="#FFFFFF" stroke-width="2" />
                                        <path
                                            d="M15 18C15.55 18 16 18.45 16 19C16 19.55 15.55 20 15 20C14.45 20 14 19.55 14 19C14 18.45 14.45 18 15 18Z"
                                            fill="#FFFFFF" />
                                        <path
                                            d="M9 18C9.55 18 10 18.45 10 19C10 19.55 9.55 20 9 20C8.45 20 8 19.55 8 19C8 18.45 8.45 18 9 18Z"
                                            fill="#FFFFFF" />
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
                                        <circle cx="12" cy="7" r="4" stroke="#FFFFFF"
                                            stroke-width="2" fill="none" />
                                        <path d="M4 17C4 14.79 5.79 13 8 13H16C18.21 13 20 14.79 20 17"
                                            stroke="#FFFFFF" stroke-width="2" />
                                        <path d="M8 13V15C8 15.55 8.45 16 9 16H15C15.55 16 16 15.55 16 15V13"
                                            stroke="#FFFFFF" stroke-width="2" />
                                        <path
                                            d="M18 13C18 14.66 16.66 16 15 16C13.34 16 12 14.66 12 13C12 11.34 13.34 10 15 10C16.66 10 18 11.34 18 13Z"
                                            stroke="#FFFFFF" stroke-width="2" />
                                        <path d="M15 13L18 16M18 16L19 15M18 16L19 17" stroke="#FFFFFF"
                                            stroke-width="2" />
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
                    @if (Auth::guard('company')->check() == true)
                        <div x-data="{ openDocs: {{ Request::is('salesreport*', 'cashflow*','profitloss*') ? 'true' : 'false' }} }">
                            <button @click="openDocs = !openDocs"
                                class="flex items-center justify-between w-full px-4 py-3 hover:bg-gray-700 group">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 12h18M3 6h18M3 18h18" />
                                        <path d="M12 3v18" />
                                    </svg>
                                    <span
                                        :class="open ? 'ml-2' :
                                            'hidden group-hover:block absolute left-16 bg-gray-800 px-2 py-1 rounded text-sm'">
                                        Report
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

                                @if (Auth::guard('company')->check() == true)
                                    <a href="/salesreport"
                                        class="block px-4 py-3 hover:bg-gray-700 flex items-center group {{ Request::is('salesreport*') ? 'bg-gray-700' : '' }}">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m9 14.25 6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                        <span
                                            :class="open ? 'block' :
                                                'hidden group-hover:block absolute left-20 bg-gray-800 px-2 py-1 rounded text-sm'">
                                            Report Transaction
                                        </span>
                                    </a>
                                @endif
                            </div>
                            <div x-show="openDocs" class="ml-6 space-y-2">

                                @if (Auth::guard('company')->check() == true)
                                    <a href="/cashflow"
                                        class="block px-4 py-3 hover:bg-gray-700 flex items-center group {{ Request::is('cashflow*') ? 'bg-gray-700' : '' }}">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m9 14.25 6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                        <span
                                            :class="open ? 'block' :
                                                'hidden group-hover:block absolute left-20 bg-gray-800 px-2 py-1 rounded text-sm'">
                                            Report Cash Flow
                                        </span>
                                    </a>
                                @endif
                            </div>
                            <div x-show="openDocs" class="ml-6 space-y-2">

                                @if (Auth::guard('company')->check() == true)
                                    <a href="/profitloss"
                                        class="block px-4 py-3 hover:bg-gray-700 flex items-center group {{ Request::is('profitloss*') ? 'bg-gray-700' : '' }}">
                                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                                            stroke-width="1.5" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m9 14.25 6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                        <span
                                            :class="open ? 'block' :
                                                'hidden group-hover:block absolute left-20 bg-gray-800 px-2 py-1 rounded text-sm'">
                                            Profit Loss Statement
                                        </span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </nav>
            </div>

        </div>
        <!-- Main Content -->
        <div :class="open ? 'ml-64' : 'ml-20'"
            class="flex-1 flex flex-col transition-all duration-300 bg-gray-100 text-gray-900">
            <header class="bg-gray-800 text-white shadow p-4 flex justify-between items-center">
                <h1 class="text-xl font-bold">@yield('title')</h1>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center hover:bg-gray-700 px-4 py-2 rounded">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z">
                            </path>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-gray-800 rounded shadow-lg z-10">
                        @if (Auth::guard('company')->check() == true)
                            <a href="{{ route('profile.index') }}"
                                class="block px-4 py-2 hover:bg-gray-700">Profile</a>
                            <form id="delete-account-form" action="{{ route('company.destroy') }}" method="POST"
                                class="relative">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete()"
                                    class="block w-full text-left px-4 py-2 hover:bg-red-700">Delete Account</button>
                            </form>
                        @endif

                        <!-- Logout -->
                        @if (Auth::guard('company')->check() == true)
                            <form action="{{ route('logout') }}" method="POST" class="relative">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 hover:bg-gray-700">Logout</button>
                            </form>
                        @endif
                        @if (Auth::guard('web')->check() == true)
                            <form action="{{ route('logoutUser') }}" method="POST" class="relative">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 hover:bg-gray-700">Logout</button>
                            </form>
                        @endif
                    </div>
                </div>
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
