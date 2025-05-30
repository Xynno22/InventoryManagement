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
    }
</style>
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-6 py-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 animate-fade-in">
            <div class="flex items-center gap-3">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Business Dashboard</h1>
                    <p class="text-sm text-gray-600">Overview of today’s business performance</p>
                </div>
            </div>
        </div>


        <!-- Low Stock Alert -->
        <div id="lowStockAlert"
            class="hidden bg-yellow-50 border border-yellow-400 text-yellow-800 px-5 py-4 mb-6 rounded-lg shadow-md cursor-pointer transition duration-200 hover:scale-[1.02] hover:bg-yellow-100 animate-fade-in">
            <div class="flex items-start gap-3">
                <div class="text-2xl">⚠️</div>
                <div>
                    <span id="lowStockCount" class="font-semibold">0 products low in stock</span>
                    <p class="text-sm">
                        click to
                        view details.
                    </p>
                </div>
            </div>
        </div>


        <div id="lowStockModal"
            class="fixed inset-0 z-50 hidden bg-black bg-opacity-40 flex items-center justify-center px-4">
            <div
                class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 transform transition-all scale-95 opacity-0 modal-enter">
                <div class="flex justify-between items-center mb-5 border-b pb-3">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M4.93 4.93l14.14 14.14M12 2a10 10 0 100 20 10 10 0 000-20z" />
                        </svg>
                        Low Stock Products
                    </h2>
                    <button id="closeModal"
                        class="text-gray-400 hover:text-red-500 transition text-xl font-bold leading-none">&times;</button>
                </div>
                <ul id="lowStockModalList"
                    class="list-disc list-inside space-y-2 text-sm text-gray-700 max-h-64 overflow-y-auto pr-2">
                    <!-- Dynamic product list -->
                </ul>
            </div>
        </div>


        <!-- Stat Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            <!-- Total Sales Today -->
            <div class="bg-gradient-to-r from-green-50 to-white border border-green-200 p-5 rounded-2xl shadow-sm">
                <div class="flex items-center text-green-700 font-semibold mb-2">
                    <svg class="h-5 w-5 mr-2 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path d="M9 2a7 7 0 100 14A7 7 0 009 2zM8 10V5h2v5H8zm0 2h2v2H8v-2z" />
                    </svg>
                    Total Sales Today
                </div>
                <div class="text-2xl sm:text-4xl font-bold text-green-800">{{ $totalSalesToday }}</div>
                <div class="text-sm text-green-600 mt-1">Sales count updated</div>
            </div>

            <!-- Income -->
            <div class="bg-gradient-to-r from-green-50 to-white border border-green-200 p-5 rounded-2xl shadow-sm">
                <div class="flex items-center text-green-700 font-semibold mb-2">
                    <svg class="h-5 w-5 mr-2 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path
                            d="M11 17a1 1 0 01-2 0v-1H7a1 1 0 010-2h2v-1H7a1 1 0 010-2h2V9H7a1 1 0 010-2h2V6a1 1 0 012 0v1h2a1 1 0 110 2h-2v1h2a1 1 0 010 2h-2v1h2a1 1 0 010 2h-2v1z" />
                    </svg>
                    Income
                </div>
                <div class="text-2xl sm:text-4xl font-bold text-green-800">Rp
                    {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="text-sm text-green-600 mt-1">Today’s earnings</div>
            </div>

            <!-- Pending Invoice -->
            <div class="bg-gradient-to-r from-red-100 to-white border border-red-300 p-5 rounded-2xl shadow-sm">
                <div class="flex items-center text-red-600 font-medium mb-2">
                    <svg class="h-5 w-5 mr-2 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-12.25a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0v-4.5zM10 13.75a1 1 0 100-2 1 1 0 000 2z"
                            clip-rule="evenodd" />
                    </svg>
                    Pending Invoice
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-red-700">{{ $totalPending }}</div>
                <div class="text-xs text-red-500 mt-1">Awaiting completion</div>
            </div>
        </div>

        <!-- Revenue Chart Section -->
        <div class="bg-white rounded-2xl shadow-md p-6 mb-10 overflow-x-auto">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-3">
                <h2 class="text-xl font-semibold text-gray-800">Revenue Overview</h2>
                <select id="revenueFilter"
                    class="w-full sm:w-auto border border-gray-300 text-gray-700 text-sm px-4 py-2 rounded-md">
                    <option value="weekly">Weekly</option>
                    <option value="monthly" selected>Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <canvas id="revenueChart" height="200" class="max-w-full"></canvas>
            <p id="noDataMessage" class="text-center text-sm text-gray-500 mt-4 hidden">No data available for this period.
            </p>
        </div>
        <!-- Filters -->
        <div class="mb-4 text-right">
            <select id="salesFilter" class="border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="1y">Last 1 Year</option>
                <option value="6m">Last 6 Months</option>
                <option value="3m">Last 3 Months</option>
            </select>
        </div>

        <div class="flex flex-col sm:flex-row sm:space-x-6 space-y-4 sm:space-y-0 mb-6">
            <div id="topCategories" class="w-full sm:w-1/2"></div>
            <div id="topProducts" class="w-full sm:w-1/2"></div>
        </div>
        <div class="bg-white rounded-2xl shadow-md p-6 mb-10 overflow-x-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">🧾 Recent Orders</h2>
                <a href="{{ route('transaction.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
            </div>
            <table class="min-w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Voucher No</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-left">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($recentOrders as $index => $transaction)
                        <tr class="border-gray-200 border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-3">{{ $transaction->voucher_code }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $transaction->customer_name }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-lg
                                                 {{ $transaction->type->name === 'Selling' ? 'bg-red-50 text-red-600 font-semibold' : ($transaction->type->name === 'Buying' ? 'bg-green-50 text-green-600' : 'bg-gray-300 text-gray-700') }}">
                                    {{ $transaction->type->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="py-1 flex items-center">
                                    @if ($transaction->status->name === 'Success')
                                        <div class="flex gap-1 items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-5 text-green-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                            </svg>
                                            <span
                                                class="text-green-500 font-medium">{{ $transaction->status->name }}</span>
                                        </div>
                                    @elseif ($transaction->status->name === 'Pending')
                                        <div class="flex gap-1 items-center justify-center">
                                            <svg xmlns="http://www.w35org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-5 text-yellow-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                            </svg>
                                            <span
                                                class="text-yellow-500 font-medium">{{ $transaction->status->name }}</span>
                                        </div>
                                    @else
                                        <span>{{ $transaction->status->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-left">
                                <span>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($recentOrders->isEmpty())
                <p class="text-sm text-gray-500 text-center py-4">No recent orders found.</p>
            @endif

        </div>
        <div class="flex flex-col lg:flex-row gap-6 mb-10">
            <!-- Payment Pie Chart -->
            <div class="bg-white rounded-2xl shadow-md p-6 w-full lg:w-2/3">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Payment Type Distribution</h2>
                    <select id="paymentPieFilter" class="border border-gray-300 text-sm px-3 py-1 rounded-md">
                        <option value="7d">Last 7 Days</option>
                        <option value="30d" selected>Last 30 Days</option>
                        <option value="3m">Last 3 Months</option>
                        <option value="1y">Last 1 Year</option>
                    </select>
                </div>
                <canvas id="paymentPieChart" height="200"></canvas>
                <p id="noPieDataMessage" class="text-center text-sm text-gray-500 mt-4 hidden">
                    No data available for this period.
                </p>
            </div>

            <!-- Expiring Promotions -->
            <div class="bg-white p-5 rounded-2xl shadow-md w-full lg:w-1/3 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">🕒 Expiring Promotions</h2>
                        <a href="{{ route('promo.index') }}"
                            class="text-sm text-blue-600 hover:underline hover:text-blue-800 transition duration-200">
                            View All
                        </a>
                    </div>
                    <ul class="divide-y divide-gray-200 text-sm text-gray-700 max-h-64 overflow-y-auto pr-1">
                        @forelse ($promo as $item)
                            <li class="py-2 flex justify-between items-center">
                                <span class="font-medium text-gray-800">{{ $item->name }}</span>
                                <span class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}
                                </span>
                            </li>
                        @empty
                            <li class="py-2 text-gray-500 italic">No upcoming expiring promos.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
        <div class="max-w-2xl p-6 bg-white rounded-2xl shadow-lg ring-1 ring-gray-200">
            <h3 class="text-2xl font-semibold text-gray-800 mb-5 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-indigo-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h1l3-6 4 8 3-6 3 6 2-4 1 3" />
                </svg>
                Stock Movement Summary Today
            </h3>

            @if ($stockMovements->isEmpty())
                <p class="text-center text-gray-400 italic text-lg py-12">No stock movement data available for today.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto border-collapse">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th class="text-left text-indigo-700 font-semibold px-6 py-3 border-b border-indigo-200">
                                    Product Name</th>
                                <th class="text-right text-indigo-700 font-semibold px-6 py-3 border-b border-indigo-200">
                                    Stock In</th>
                                <th class="text-right text-indigo-700 font-semibold px-6 py-3 border-b border-indigo-200">
                                    Stock Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stockMovements as $movement)
                                <tr class="hover:bg-indigo-50 transition-colors duration-150 cursor-pointer">
                                    <td class="px-6 py-4 border-b border-indigo-100 text-gray-900 font-medium">
                                        {{ $movement['product_name'] }}</td>
                                    <td
                                        class="px-6 py-4 border-b border-indigo-100 text-right text-green-600 font-semibold">
                                        {{ number_format($movement['total_out']) }}</td>
                                    <td class="px-6 py-4 border-b border-indigo-100 text-right text-red-600 font-semibold">
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
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
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
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
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
                let catHtml = `<div class="bg-gradient-to-r from-gray-300 via-gray-100 to-white border border-gray-400 p-5 rounded-2xl">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Top 5 Best-Selling Categories</h3>
                <ul class="divide-y divide-gray-300 text-sm text-gray-700">`;

                if (response.topCategories.length === 0) {
                    catHtml += `<li class="py-2 text-gray-500 italic text-center">No data available</li>`;
                } else {
                    response.topCategories.forEach(item => {
                        catHtml += `<li class="flex justify-between py-2">
                        <span class="truncate max-w-[70%] font-medium">${item.name}</span>
                        <span class="font-semibold text-gray-800">${item.total_sold} Orders</span>
                    </li>`;
                    });
                }

                catHtml += '</ul></div>';
                $('#topCategories').html(catHtml);

                let prodHtml = `<div class="bg-gradient-to-r from-gray-300 via-gray-100 to-white border border-gray-400 p-5 rounded-2xl">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Top 5 Best-Selling Products</h3>
                <ul class="divide-y divide-gray-300 text-sm text-gray-700">`;

                if (response.topProducts.length === 0) {
                    prodHtml += `<li class="py-2 text-gray-500 italic text-center">No data available</li>`;
                } else {
                    response.topProducts.forEach(item => {
                        prodHtml += `<li class="flex justify-between py-2">
                        <span class="truncate max-w-[70%] font-medium">${item.name}</span>
                        <span class="font-semibold text-gray-800">${item.total_sold} Orders</span>
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
                        $('#lowStockModalList').append(`<li>${product.name} - ${product.stock} left</li>`);
                    });

                    $('#lowStockAlert').removeClass('hidden');
                } else {
                    $('#lowStockAlert').addClass('hidden');
                }
            });
        }

        $(document).ready(function() {
            renderChart('monthly');
            loadTopSales();
            checkLowStock();

            $('#revenueFilter').on('change', (e) => renderChart(e.target.value));
            $('#salesFilter').on('change', function() {
                loadTopSales($(this).val());
            });

            // Modal toggle
            $('#lowStockAlert').on('click', function() {
                $('#lowStockModal').removeClass('hidden');
            });

            $('#closeModal, #lowStockModal').on('click', function(e) {
                // Prevent closing when clicking inside modal box
                if (e.target.id === 'lowStockModal' || e.target.id === 'closeModal') {
                    $('#lowStockModal').addClass('hidden');
                }
            });
        });

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

                const labels = response.map(item => item.name); // <-- sebelumnya: item.payment_type
                const data = response.map(item => item.total);

                pieChartInstance = new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Payment Type',
                            data,
                            backgroundColor: ['#4ade80', '#fbbf24', '#60a5fa', '#f87171',
                                '#a78bfa'
                            ],
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            });
        }

        $(document).ready(function() {
            loadPaymentPie();
            $('#paymentPieFilter').on('change', function() {
                loadPaymentPie($(this).val());
            });
        });
    </script>
@endsection
