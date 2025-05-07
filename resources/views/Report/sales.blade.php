@extends('layouts.app')

@section('title', 'Transaction Report')

@section('content')
    <div class="max-w-7xl mx-auto p-6 bg-white rounded-lg shadow mt-6">
        <!-- Filter -->
        <div class="max-w-7xl mx-auto mt-6 p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <form method="GET" action="{{ route('reports.sales') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="form-input w-full focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="form-input w-full focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                        <select name="type_id" class="form-select w-full focus:outline-none">
                            <option value="">All Types</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <select name="payment_id" class="form-select w-full focus:outline-none">
                            <option value="">All Payments</option>
                            @foreach ($payments as $payment)
                                <option value="{{ $payment->id }}"
                                    {{ request('payment_id') == $payment->id ? 'selected' : '' }}>
                                    {{ $payment->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status_id" class="form-select w-full focus:outline-none">
                            <option value="">All Statuses</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}"
                                    {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search Customer</label>
                        <input type="text" name="customer" value="{{ request('customer') }}" placeholder="John Smith..."
                            class="form-input w-full focus:outline-none">
                    </div>
                </div>

                <div class="flex gap-2 justify-between mt-5">
                    <!-- Filter button -->
                    <button class="bg-indigo-600 text-white px-4 py-2 rounded">Filter</button>
                </div>
            </form>
        </div>

        <!-- Export Button (outside the form) -->
        <div class="flex justify-end mt-4">
            <form action="{{ route('transactions.export.pdf') }}" method="GET">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <input type="hidden" name="type_id" value="{{ request('type_id') }}">
                <input type="hidden" name="payment_id" value="{{ request('payment_id') }}">
                <input type="hidden" name="status_id" value="{{ request('status_id') }}">
                <input type="hidden" name="customer" value="{{ request('customer') }}">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Export PDF
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full table-auto border border-gray-200">
                <thead class="bg-gray-200 text-gray-700 text-sm font-semibold">
                    <tr>
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-left">Invoice</th>
                        <th class="px-4 py-2 text-left">Customer</th>
                        <th class="px-4 py-2 text-left">Total</th>
                        <th class="px-4 py-2 text-left">Payment</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    @forelse ($transactions as $tx)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($tx->date)->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2 font-semibold">{{ $tx->voucher_code }}</td>
                            <td class="px-4 py-2">{{ $tx->customer_name }}</td>
                            <td class="px-4 py-2 text-left">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-2">{{ $tx->payment->name ?? '-' }}</td>
                            <td class="px-4 py-2">
                                @php
                                    $status = strtolower($tx->status->name ?? '-');
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
    </div>
@endsection
