@extends('layouts.app')

@section('title', 'Profit Loss Statement')

@section('content')
    <div class="max-w-7xl mx-auto p-6 bg-white rounded-lg shadow mt-6">
        <!-- Filter -->
        <div class="max-w-7xl mx-auto mt-6 p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <form method="GET" action="{{ route('reports.profitloss') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                            class="form-input w-full focus:outline-none border-gray-300 rounded">
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                            class="form-input w-full focus:outline-none border-gray-300 rounded">
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label for="payment_id" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <select id="payment_id" name="payment_id"
                            class="form-select w-full focus:outline-none border-gray-300 rounded">
                            <option value="">All Payments</option>
                            @foreach ($payments as $payment)
                                <option value="{{ $payment->id }}"
                                    {{ request('payment_id') == $payment->id ? 'selected' : '' }}>
                                    {{ $payment->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status_id" name="status_id"
                            class="form-select w-full focus:outline-none border-gray-300 rounded">
                            <option value="">All Statuses</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}"
                                    {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded shadow text-sm font-medium">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="flex justify-end gap-5 mt-4">
            <form action="{{ route('profitloss.export.pdf') }}" method="GET">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <input type="hidden" name="type_id" value="{{ request('type_id') }}">
                <input type="hidden" name="payment_id" value="{{ request('payment_id') }}">
                <input type="hidden" name="status_id" value="{{ request('status_id') }}">
                <input type="hidden" name="customer" value="{{ request('customer') }}">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 focus:outline-none">
                    Export PDF
                </button>
            </form>
            <form action="{{ route('profitloss.export.excel') }}" method="GET">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <input type="hidden" name="type_id" value="{{ request('type_id') }}">
                <input type="hidden" name="payment_id" value="{{ request('payment_id') }}">
                <input type="hidden" name="status_id" value="{{ request('status_id') }}">
                <input type="hidden" name="customer" value="{{ request('customer') }}">
                <button type="submit"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 focus:outline-none">
                    Export Excel
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full table-auto border border-gray-200">
                <thead class="bg-gray-200 text-gray-700 text-sm font-semibold">
                    <tr>
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-left">Product Name</th>
                        <th class="px-4 py-2 text-left">Note</th>
                        <th class="px-4 py-2 text-left">Income</th>
                        <th class="px-4 py-2 text-left">Outcome</th>
                        <th class="px-4 py-2 text-left">Payment</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    @forelse ($transactions as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $item['date'] }}</td>
                            <td class="px-4 py-2 font-semibold">{{ $item['product_name'] }}</td>
                            <td class="px-4 py-2">{{ $item['note'] }}</td>
                            <td class="px-4 py-2">Rp {{ number_format($item['cash_in'], 0, ',', '.') }}</td>
                            <td class="px-4 py-2">Rp {{ number_format($item['cash_out'], 0, ',', '.') }}</td>
                            <td class="px-4 py-2 font-semibold">{{ $item['payment_method'] }}</td>
                            <td class="px-4 py-2">
                                @php
                                    $status = strtolower($item['status'] ?? '-');
                                    $statusColor = match ($status) {
                                        'success' => 'text-green-600 font-semibold',
                                        'pending' => 'text-red-600 font-semibold',
                                        default => 'text-gray-600',
                                    };
                                @endphp
                                <span class="{{ $statusColor }}">{{ ucfirst($status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Total Cash Flow -->
        <div class="mt-6 p-6 bg-gray-50 border border-gray-200 rounded-lg max-w-md ml-auto shadow-md">
            <h2 class="text-xl font-semibold mb-4 border-b border-gray-300 pb-2">Total Cash Flow</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-700 font-medium">Total Cash In:</span>
                    <span class="font-semibold text-green-600">Rp {{ number_format($totalCashIn, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700 font-medium">Total Cash Out:</span>
                    <span class="font-semibold text-red-600">Rp {{ number_format($totalCashOut, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t border-gray-300 pt-3">
                    <span class="text-gray-800 font-semibold">Profit:</span>
                    <span
                        class="font-bold text-lg {{ $totalCashIn - $totalCashOut >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        Rp {{ number_format($totalCashIn - $totalCashOut, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

    </div>
@endsection
