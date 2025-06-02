@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    Dashboard
@endsection
<style>
    @layer utilities {
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-enter {
            animation: modalFade 0.3s ease-out forwards;
        }

        @keyframes modalFade {
            from {
                transform: scale(0.95);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Mobile scroll improvements */
        @media (max-width: 768px) {
            .mobile-scroll {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
            }
            
            .mobile-scroll::-webkit-scrollbar {
                height: 4px;
            }
            
            .mobile-scroll::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 2px;
            }
            
            .mobile-scroll::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 2px;
            }
        }
    }
</style>
@section('content')
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-6 py-4 sm:py-6">
        <!-- Header Section -->
        <div class="flex flex-col justify-between items-start gap-3 sm:gap-4 mb-6 sm:mb-8 animate-fade-in">
            <div class="flex items-center gap-2 sm:gap-3 w-full">
                <div class="flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Business Dashboard</h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Overview of today's business performance</p>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div id="lowStockAlert"
            class="hidden bg-yellow-50 border border-yellow-400 text-yellow-800 px-4 sm:px-5 py-3 sm:py-4 mb-4 sm:mb-6 rounded-lg shadow-md cursor-pointer transition duration-200 hover:scale-[1.02] hover:bg-yellow-100 animate-fade-in">
            <div class="flex items-start gap-2 sm:gap-3">
                <div class="text-xl sm:text-2xl">⚠️</div>
                <div class="flex-1 min-w-0">
                    <span id="lowStockCount" class="font-semibold text-sm sm:text-base">0 products low in stock</span>
                    <p class="text-xs sm:text-sm mt-1">
                        Tap to view details.
                    </p>
                </div>
            </div>
        </div>

        <!-- Low Stock Modal -->
        <div id="lowStockModal"
            class="fixed inset-0 z-50 hidden bg-black bg-opacity-40 flex items-center justify-center px-4">
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-sm sm:max-w-md p-4 sm:p-6 transform transition-all scale-95 opacity-0 modal-enter max-h-[80vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4 sm:mb-5 border-b pb-3">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M4.93 4.93l14.14 14.14M12 2a10 10 0 100 20 10 10 0 000-20z" />
                        </svg>
                        Low Stock Products
                    </h2>
                    <button id="closeModal"
                        class="text-gray-400 hover:text-red-500 transition text-xl font-bold leading-none p-1">&times;</button>
                </div>
                <ul id="lowStockModalList"
                    class="list-disc list-inside space-y-2 text-sm text-gray-700 max-h-64 overflow-y-auto pr-2">
                    <!-- Dynamic product list -->
                </ul>
            </div>
        </div>

        <!-- Stat Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-10">
            <!-- Total Sales Today -->
            <div class="bg-gradient-to-r from-green-50 to-white border border-green-200 p-4 sm:p-5 rounded-xl sm:rounded-2xl shadow-sm">
                <div class="flex items-center text-green-700 font-semibold mb-2 text-sm sm:text-base">
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 mr-2 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path d="M9 2a7 7 0 100 14A7 7 0 009 2zM8 10V5h2v5H8zm0 2h2v2H8v-2z" />
                    </svg>
                    <span class="truncate">Total Sales Today</span>
                </div>
                <div class="text-xl sm:text-2xl lg:text-4xl font-bold text-green-800">{{ $totalSalesToday }}</div>
                <div class="text-xs sm:text-sm text-green-600 mt-1">Sales count updated</div>
            </div>

            <!-- Income -->
            <div class="bg-gradient-to-r from-green-50 to-white border border-green-200 p-4 sm:p-5 rounded-xl sm:rounded-2xl shadow-sm">
                <div class="flex items-center text-green-700 font-semibold mb-2 text-sm sm:text-base">
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 mr-2 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path
                            d="M11 17a1 1 0 01-2 0v-1H7a1 1 0 010-2h2v-1H7a1 1 0 010-2h2V9H7a1 1 0 010-2h2V6a1 1 0 012 0v1h2a1 1 0 110 2h-2v1h2a1 1 0 010 2h-2v1h2a1 1 0 010 2h-2v1z" />
                    </svg>
                    <span class="truncate">Income</span>
                </div>
                <div class="text-lg sm:text-2xl lg:text-4xl font-bold text-green-800 break-words">Rp
                    {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="text-xs sm:text-sm text-green-600 mt-1">Today's earnings</div>
            </div>

            <!-- Pending Invoice -->
            <div class="bg-gradient-to-r from-red-100 to-white border border-red-300 p-4 sm:p-5 rounded-xl sm:rounded-2xl shadow-sm sm:col-span-2 lg:col-span-1">
                <div class="flex items-center text-red-600 font-medium mb-2 text-sm sm:text-base">
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 mr-2 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-12.25a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0v-4.5zM10 13.75a1 1 0 100-2 1 1 0 000 2z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="truncate">Pending Invoice</span>
                </div>
                <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-red-700">{{ $totalPending }}</div>
                <div class="text-xs sm:text-sm text-red-500 mt-1">Awaiting completion</div>
            </div>
        </div>

        <!-- Revenue Chart Section -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-md p-4 sm:p-6 mb-6 sm:mb-10">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Revenue Overview</h2>
                <select id="revenueFilter"
                    class="w-full sm:w-auto border border-gray-300 text-gray-700 text-sm px-3 sm:px-4 py-2 rounded-md">
                    <option value="weekly">Weekly</option>
                    <option value="monthly" selected">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <div class="mobile-scroll">
                <canvas id="revenueChart" height="200" class="max-w-full min-w-[300px]"></canvas>
            </div>
            <p id="noDataMessage" class="text-center text-sm text-gray-500 mt-4 hidden">No data available for this period.</p>
        </div>

        <!-- Filters -->
        <div class="mb-4 flex justify-between items-center">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Top Performance</h2>
            <select id="salesFilter" class="border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="1y">Last 1 Year</option>
                <option value="6m">Last 6 Months</option>
                <option value="3m">Last 3 Months</option>
            </select>
        </div>

        <!-- Top Categories and Products -->
        <div class="flex flex-col lg:flex-row lg:space-x-6 space-y-4 lg:space-y-0 mb-6 sm:mb-8">
            <div id="topCategories" class="w-full lg:w-1/2"></div>
            <div id="topProducts" class="w-full lg:w-1/2"></div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-xl sm:rounded-2xl shadow-md p-4 sm:p-6 mb-6 sm:mb-10">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-2">
                <h2 class="text-lg font-semibold text-gray-800">🧾 Recent Orders</h2>
                <a href="{{ route('transaction.index') }}" class="text-sm text-blue-600 hover:underline self-start sm:self-auto">View all</a>
            </div>
            
            <!-- Mobile Card View -->
            <div class="block sm:hidden space-y-3">
                @foreach ($recentOrders as $transaction)
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 truncate">{{ $transaction->customer_name }}</p>
                                <p class="text-sm text-gray-600">{{ $transaction->voucher_code }}</p>
                            </div>
                            <div class="text-right ml-2">
                                <p class="font-semibold text-gray-900">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="px-2 py-1 text-xs font-semibold rounded-lg
                                {{ $transaction->type->name === 'Selling' ? 'bg-red-50 text-red-600' : ($transaction->type->name === 'Buying' ? 'bg-green-50 text-green-600' : 'bg-gray-300 text-gray-700') }}">
                                {{ $transaction->type->name ?? '-' }}
                            </span>
                            <div class="flex items-center gap-1">
                                @if ($transaction->status->name === 'Success')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-green-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                    </svg>
                                    <span class="text-green-500 font-medium text-sm">{{ $transaction->status->name }}</span>
                                @elseif ($transaction->status->name === 'Pending')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-yellow-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                    </svg>
                                    <span class="text-yellow-500 font-medium text-sm">{{ $transaction->status->name }}</span>
                                @else
                                    <span class="text-sm">{{ $transaction->status->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop Table View -->
            <div class="hidden sm:block mobile-scroll">
                <table class="min-w-full text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 whitespace-nowrap">Voucher No</th>
                            <th class="px-4 py-3 whitespace-nowrap">Customer</th>
                            <th class="px-4 py-3 whitespace-nowrap">Type</th>
                            <th class="px-4 py-3 whitespace-nowrap">Status</th>
                            <th class="px-4 py-3 text-left whitespace-nowrap">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($recentOrders as $transaction)
                            <tr class="border-gray-200 border-b hover:bg-gray-50 transition">
                                <td class="px-4 py-3 whitespace-nowrap">{{ $transaction->voucher_code }}</td>
                                <td class="px-4 py-3 font-semibold whitespace-nowrap">{{ $transaction->customer_name }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-lg
                                        {{ $transaction->type->name === 'Selling' ? 'bg-red-50 text-red-600 font-semibold' : ($transaction->type->name === 'Buying' ? 'bg-green-50 text-green-600' : 'bg-gray-300 text-gray-700') }}">
                                        {{ $transaction->type->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="py-1 flex items-center">
                                        @if ($transaction->status->name === 'Success')
                                            <div class="flex gap-1 items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-green-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                                </svg>
                                                <span class="text-green-500 font-medium">{{ $transaction->status->name }}</span>
                                            </div>
                                        @elseif ($transaction->status->name === 'Pending')
                                            <div class="flex gap-1 items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-yellow-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                                </svg>
                                                <span class="text-yellow-500 font-medium">{{ $transaction->status->name }}</span>
                                            </div>
                                        @else
                                            <span>{{ $transaction->status->name }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-left whitespace-nowrap">
                                    <span>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($recentOrders->isEmpty())
                <p class="text-sm text-gray-500 text-center py-8">No recent orders found.</p>
            @endif
        </div>

        <!-- Payment Chart and Promotions -->
        <div class="flex flex-col lg:flex-row gap-4 sm:gap-6 mb-6 sm:mb-10">
            <!-- Payment Pie Chart -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-md p-4 sm:p-6 w-full lg:w-2/3">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-2">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Payment Type Distribution</h2>
                    <select id="paymentPieFilter" class="w-full sm:w-auto border border-gray-300 text-sm px-3 py-1 rounded-md">
                        <option value="7d">Last 7 Days</option>
                        <option value="30d" selected>Last 30 Days</option>
                        <option value="3m">Last 3 Months</option>
                        <option value="1y">Last 1 Year</option>
                    </select>
                </div>
                <div class="mobile-scroll">
                    <canvas id="paymentPieChart" height="200" class="min-w-[250px]"></canvas>
                </div>
                <p id="noPieDataMessage" class="text-center text-sm text-gray-500 mt-4 hidden">
                    No data available for this period.
                </p>
            </div>

            <!-- Expiring Promotions -->
            <div class="bg-white p-4 sm:p-5 rounded-xl sm:rounded-2xl shadow-md w-full lg:w-1/3 flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-2">
                        <h2 class="text-lg font-semibold text-gray-800">🕒 Expiring Promotions</h2>
                        <a href="{{ route('promo.index') }}"
                            class="text-sm text-blue-600 hover:underline hover:text-blue-800 transition duration-200 self-start sm:self-auto">
                            View All
                        </a>
                    </div>
                    <ul class="divide-y divide-gray-200 text-sm text-gray-700 max-h-64 overflow-y-auto pr-1">
                        @forelse ($promo as $item)
                            <li class="py-2 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1">
                                <span class="font-medium text-gray-800 truncate">{{ $item->name }}</span>
                                <span class="text-xs text-gray-500 self-start sm:self-auto">
                                    {{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}
                                </span>
                            </li>
                        @empty
                            <li class="py-4 text-gray-500 italic text-center">No upcoming expiring promos.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Stock Movement Summary -->
        <div class="bg-white p-4 sm:p-6 rounded-xl sm:rounded-2xl shadow-lg ring-1 ring-gray-200">
            <h3 class="text-lg sm:text-2xl font-semibold text-gray-800 mb-4 sm:mb-5 flex items-center gap-2 sm:gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7 text-indigo-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h1l3-6 4 8 3-6 3 6 2-4 1 3" />
                </svg>
                <span class="break-words">Stock Movement Summary Today</span>
            </h3>

            @if ($stockMovements->isEmpty())
                <p class="text-center text-gray-400 italic text-base sm:text-lg py-8 sm:py-12">No stock movement data available for today.</p>
            @else
                <!-- Mobile Card View -->
                <div class="block sm:hidden space-y-3">
                    @foreach ($stockMovements as $movement)
                        <div class="border border-indigo-200 rounded-lg p-3 bg-indigo-50">
                            <h4 class="font-medium text-gray-900 mb-2 truncate">{{ $movement['product_name'] }}</h4>
                            <div class="flex justify-between text-sm">
                                <div class="text-green-600">
                                    <span class="font-semibold">Stock In:</span> {{ number_format($movement['total_out']) }}
                                </div>
                                <div class="text-red-600">
                                    <span class="font-semibold">Stock Out:</span> {{ number_format($movement['total_in']) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop Table View -->
                <div class="hidden sm:block mobile-scroll">
                    <table class="min-w-full table-auto border-collapse">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="text-left text-indigo-700 font-semibold px-4 sm:px-6 py-3 border-b border-indigo-200 whitespace-nowrap">
                                    Product Name</th>
                                <th class="text-right text-indigo-700 font-semibold px-4 sm:px-6 py-3 border-b border-indigo-200 whitespace-nowrap">
                                    Stock In</th>
                                <th class="text-right text-indigo-700 font-semibold px-4 sm:px-6 py-3 border-b border-indigo-200 whitespace-nowrap">
                                    Stock Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stockMovements as $movement)
                                <tr class="hover:bg-indigo-50 transition-colors duration-150 cursor-pointer">
                                    <td class="px-4 sm:px-6 py-4 border-b border-indigo-100 text-gray-900 font-medium">
                                        {{ $movement['product_name'] }}</td>
                                    <td class="px-4 sm:px-6 py-4 border-b border-indigo-100 text-right text-green-600 font-semibold whitespace-nowrap">
                                        {{ number_format($movement['total_out']) }}</td>
                                    <td class="px-4 sm:px-6 py-4 border-b border-indigo-100 text-right text-red-600 font-semibold whitespace-nowrap">
                                        {{ number_format($movement['total_in']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Script Section -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        const chartData = @json($revenueChartData);
        let chartInstance;
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const noDataMessage = document.getElementById('noDataMessage');

        function renderChart(type) {
            const {
                labels,
                data
            } = chartData[type] ?? {
                labels: [],
                data: []
            };
            if (chartInstance) chartInstance.destroy();

            if (!data || data.length === 0) {
                noDataMessage.classList.remove('hidden');
                return;
            } else {
                noDataMessage.classList.add('hidden');
            }

            chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data,
                        borderColor: '#6B7280',
                        backgroundColor: 'rgba(107, 114, 128, 0.15)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: window.innerWidth < 768 ? 2 : 3,
                        pointHoverRadius: window.innerWidth < 768 ? 4 : 6,
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (window.innerWidth < 768) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                    }
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                },
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                },
                                maxRotation: window.innerWidth < 768 ? 45 : 0
                            }
                        }
                    }
                }
            });
        }

        function loadTopSales(range = '1y') {
            $.get('/dashboard/top-sales', {
                range
            }, function(response) {
                let catHtml = `<div class="bg-gradient-to-r from-gray-300 via-gray-100 to-white border border-gray-400 p-4 sm:p-5 rounded-xl sm:rounded-2xl">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Top 5 Best-Selling Categories</h3>
                <ul class="divide-y divide-gray-300 text-sm text-gray-700">`;

                if (response.topCategories.length === 0) {
                    catHtml += `<li class="py-2 text-gray-500 italic text-center">No data available</li>`;
                } else {
                    response.topCategories.forEach(item => {
                        catHtml += `<li class="flex justify-between py-2">
                        <span class="truncate max-w-[60%] sm:max-w-[70%] font-medium">${item.name}</span>
                        <span class="font-semibold text-gray-800 text-xs sm:text-sm">${item.total_sold} Orders</span>
                    </li>`;
                    });
                }

                catHtml += '</ul></div>';
                $('#topCategories').html(catHtml);

                let prodHtml = `<div class="bg-gradient-to-r from-gray-300 via-gray-100 to-white border border-gray-400 p-4 sm:p-5 rounded-xl sm:rounded-2xl">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Top 5 Best-Selling Products</h3>
                <ul class="divide-y divide-gray-300 text-sm text-gray-700">`;

                if (response.topProducts.length === 0) {
                    prodHtml += `<li class="py-2 text-gray-500 italic text-center">No data available</li>`;
                } else {
                    response.topProducts.forEach(item => {
                        prodHtml += `<li class="flex justify-between py-2">
                        <span class="truncate max-w-[60%] sm:max-w-[70%] font-medium">${item.name}</span>
                        <span class="font-semibold text-gray-800 text-xs sm:text-sm">${item.total_sold} Orders</span>
                    </li>`;
                    });
                }

                prodHtml += '</ul></div>';
                $('#topProducts').html(prodHtml);
            });
        }

        function checkLowStock() {
            $.get('/dashboard/low-stock', function(response) {
                const products = response.products || [];
                if (products.length > 0) {
                    $('#lowStockCount').text(
                        `${products.length} product${products.length > 1 ? 's' : ''} low in stock`);

                    // Simpan produk di modal
                    $('#lowStockModalList').empty();
                    products.forEach(product => {
                        $('#lowStockModalList').append(`<li class="text-sm">${product.name} - ${product.stock} left</li>`);
                    });

                    $('#lowStockAlert').removeClass('hidden');
                } else {
                    $('#lowStockAlert').addClass('hidden');
                }
            });
        }

        let pieChartInstance;
        const pieCtx = document.getElementById('paymentPieChart').getContext('2d');
        const pieNoData = document.getElementById('noPieDataMessage');

        function loadPaymentPie(range = '30d') {
            $.get('/dashboard/payment-types', {
                range
            }, function(response) {
                if (pieChartInstance) pieChartInstance.destroy();

                if (response.length === 0) {
                    pieNoData.classList.remove('hidden');
                    return;
                } else {
                    pieNoData.classList.add('hidden');
                }

                const labels = response.map(item => item.name);
                const data = response.map(item => item.total);

                pieChartInstance = new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Payment Type',
                            data,
                            backgroundColor: ['#4ade80', '#fbbf24', '#60a5fa', '#f87171', '#a78bfa'],
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: window.innerWidth < 768 ? 'bottom' : 'bottom',
                                labels: {
                                    font: {
                                        size: window.innerWidth < 768 ? 10 : 12
                                    },
                                    padding: window.innerWidth < 768 ? 10 : 20
                                }
                            }
                        }
                    }
                });
            });
        }

        $(document).ready(function() {
            renderChart('monthly');
            loadTopSales();
            checkLowStock();
            loadPaymentPie();

            $('#revenueFilter').on('change', (e) => renderChart(e.target.value));
            $('#salesFilter').on('change', function() {
                loadTopSales($(this).val());
            });
            $('#paymentPieFilter').on('change', function() {
                loadPaymentPie($(this).val());
            });

            // Modal toggle
            $('#lowStockAlert').on('click', function() {
                $('#lowStockModal').removeClass('hidden');
                $('body').addClass('overflow-hidden');
            });

            $('#closeModal, #lowStockModal').on('click', function(e) {
                // Prevent closing when clicking inside modal box
                if (e.target.id === 'lowStockModal' || e.target.id === 'closeModal') {
                    $('#lowStockModal').addClass('hidden');
                    $('body').removeClass('overflow-hidden');
                }
            });

            // Handle window resize for responsive charts
            $(window).on('resize', function() {
                if (chartInstance) {
                    chartInstance.options.scales.y.ticks.font.size = window.innerWidth < 768 ? 10 : 12;
                    chartInstance.options.scales.x.ticks.font.size = window.innerWidth < 768 ? 10 : 12;
                    chartInstance.options.scales.x.ticks.maxRotation = window.innerWidth < 768 ? 45 : 0;
                    chartInstance.data.datasets[0].pointRadius = window.innerWidth < 768 ? 2 : 3;
                    chartInstance.data.datasets[0].pointHoverRadius = window.innerWidth < 768 ? 4 : 6;
                    chartInstance.update();
                }
                
                if (pieChartInstance) {
                    pieChartInstance.options.plugins.legend.labels.font.size = window.innerWidth < 768 ? 10 : 12;
                    pieChartInstance.options.plugins.legend.labels.padding = window.innerWidth < 768 ? 10 : 20;
                    pieChartInstance.update();
                }
            });
        });
    </script>
@endsection