@extends('layouts.app')
@section('title', 'Compare Note with Transaction')

@section('content')
    <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Compare Note with Transaction</h1>
            <div class="flex space-x-2">
                <a href="{{ route('note.index') }}"
                   class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                    Back to List
                </a>
            </div>
        </div>

        {{-- Transaction Selector --}}
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <h3 class="text-lg font-semibold text-yellow-800 mb-3">Select Transaction to Compare</h3>
            <form id="compareForm" method="GET" action="{{ route('note.compare', $note->id) }}">
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <select name="transaction_id" id="transactionSelect"
                                                      class="w-full select2 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Select Transaction --</option>
                            @foreach($transactions as $transaction)
                                <option value="{{ $transaction->id }}"
                                        {{ request('transaction_id') == $transaction->id ? 'selected' : '' }}>
                            {{ $transaction->voucher_code }} - {{ $transaction->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>

        {{-- Comparison Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Left Side - Note Details --}}
            <div class="border border-blue-200 rounded-lg">
                <div class="bg-blue-50 px-6 py-4 border-b border-blue-200">
                    <a href="{{ route('note.show', $note->id)}}" class="text-xl underline font-bold text-blue-800 underline-offset-8 hover:text-blue-500 transition-all duration-150">NOTE</a>
                </div>

                <div class="p-6">
                    {{-- Note Basic Info --}}
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Customer Name</label>
                                <p class="text-gray-800 font-medium">{{ $note->customer_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Total Price</label>
                                <p class="text-xl font-bold text-blue-600">Rp {{ number_format($note->total_price, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Description</label>
                                <p class="text-gray-800">{{ $note->description ?? 'No description' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Created</label>
                                <p class="text-gray-800">{{ $note->created_at->format('d M Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Note Details --}}
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Items</h3>
                    @if($note->noteDetails && $note->noteDetails->count() > 0)
                        <div class="space-y-2">
                            @foreach($note->noteDetails as $index => $detail)
                                <div class="bg-white border border-gray-200 rounded-lg p-3">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-800">{{ $detail->product->name ?? 'Product not found' }}</h4>
                                            @if($detail->product && $detail->product->description)
                                                <p class="text-sm text-gray-500">{{ $detail->product->description }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right ml-4">
                                            <div class="text-sm text-gray-600">Qty: {{ number_format($detail->quantity, 0, ',', '.') }}</div>
                                            <div class="text-sm text-gray-600">@Rp {{ number_format($detail->price, 0, ',', '.') }}</div>
                                            <div class="font-medium text-blue-600">Rp {{ number_format($detail->quantity * $detail->price, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>No items found</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Side - Transaction Details --}}
            <div class="border border-green-200 rounded-lg">
                <div class="bg-green-50 px-6 py-4 border-b border-green-200">

                    @if(isset($selectedTransaction))
                        <a href="{{ route('transaction.show', $selectedTransaction->id) }}"
                           class="text-xl font-bold text-green-800 underline underline-offset-8 hover:text-green-500 transition-all duration-150">
                           TRANSACTION
                        </a>
                    @else
                        <a class="text-xl font-bold text-green-800">TRANSACTION</a>
                    @endif

                </div>

                <div class="p-6">
                    @if($selectedTransaction)
                        {{-- Transaction Basic Info --}}
                        <div class="bg-gray-50 p-4 rounded-lg mb-6">
                            <div class="grid grid-cols-1 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Customer Name</label>
                                    <p class="text-gray-800 font-medium">{{ $selectedTransaction->customer_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Total Price (After Discount)</label>
                                    <p class="text-xl font-bold text-green-600">Rp {{ number_format($selectedTransaction->total_price, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Transaction Date</label>
                                    <p class="text-gray-800">{{ \Carbon\Carbon::parse($selectedTransaction->date)->format('d M Y H:i:s') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Payment Method</label>
                                    <p class="text-gray-800">{{ $selectedTransaction->payment->name ?? 'Not specified' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Transaction Details --}}
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Items</h3>
                        @if($selectedTransaction->transactionDetails && $selectedTransaction->transactionDetails->count() > 0)
                            <div class="space-y-2">
                                @foreach($selectedTransaction->transactionDetails as $index => $detail)
                                    <div class="bg-white border border-gray-200 rounded-lg p-3">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-800">{{ $detail->product->name ?? 'Product not found' }}</h4>
                                                @if($detail->product && $detail->product->description)
                                                    <p class="text-sm text-gray-500">{{ $detail->product->description }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right ml-4">
                                                <div class="text-sm text-gray-600">Qty: {{ number_format($detail->quantity, 0, ',', '.') }}</div>
                                                <div class="text-sm text-gray-600">@Rp {{ number_format($detail->price, 0, ',', '.') }}</div>
                                                <div class="font-medium text-green-600">Rp {{ number_format($detail->quantity * $detail->price, 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <p>No items found</p>
                            </div>
                        @endif
                    @else
                        {{-- No Transaction Selected --}}
                        <div class="text-center py-16">
                            <div class="text-gray-400 mb-4">
                                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Transaction Selected</h3>
                            <p class="text-gray-500">Select a transaction from the dropdown above to compare with this note.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    {{-- Comparison Summary --}}
        @if($selectedTransaction)
            <div class="mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Comparison Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Price Difference --}}
                    <div class="text-center">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Price Difference</label>
                        @php
                            $priceDiff = $selectedTransaction->total_price - $note->total_price;
                        @endphp
                        <p class="text-2xl font-bold {{ $priceDiff > 0 ? 'text-green-600' : ($priceDiff < 0 ? 'text-red-600' : 'text-gray-600') }}">
                            {{ $priceDiff >= 0 ? '+' : '' }}Rp {{ number_format($priceDiff, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Transaction {{ $priceDiff > 0 ? 'higher' : ($priceDiff < 0 ? 'lower' : 'equal') }}
                        </p>
                    </div>

                    {{-- Item Count Comparison --}}
                    <div class="text-center">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Items Count</label>
                        <div class="flex justify-center items-center space-x-4">
                            <div class="text-center">
                                <p class="text-lg font-bold text-blue-600">{{ $note->noteDetails->count() }}</p>
                                <p class="text-xs text-gray-500">Note</p>
                            </div>
                            <div class="text-gray-400">vs</div>
                            <div class="text-center">
                                <p class="text-lg font-bold text-green-600">{{ $selectedTransaction->transactionDetails->count() }}</p>
                                <p class="text-xs text-gray-500">Transaction</p>
                            </div>
                        </div>
                    </div>

                    {{-- Customer Match --}}
                    <div class="text-center">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Customer Match</label>
                        @php
                            $customerMatch = strtolower(trim($note->customer_name)) === strtolower(trim($selectedTransaction->customer_name));
                        @endphp
                        <div class="flex justify-center items-center">
                            @if($customerMatch)
                                <div class="flex items-center text-green-600">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="font-medium">Match</span>
                                </div>
                            @else
                                <div class="flex items-center text-red-600">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span class="font-medium">Different</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{-- Action Buttons --}}
        {{-- <div class="flex justify-end space-x-2 mt-8 pt-6 border-t border-gray-200"> --}}
        {{--     @if($selectedTransaction) --}}
        {{--         <button onclick="window.print()" --}}
        {{--                 class="bg-green-500 text-white px-5 py-2 rounded-lg hover:bg-green-600 transition"> --}}
        {{--             Print Comparison --}}
        {{--         </button> --}}
        {{--         <a href="{{ route('transaction.show', $selectedTransaction->id) }}" --}}
        {{--            class="bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600 transition"> --}}
        {{--             View Transaction Detail --}}
        {{--         </a> --}}
        {{--     @endif --}}
        {{-- </div> --}}
    </div>

    <script>
        $(document).ready(function() {
            $('#transactionSelect').select2({
                placeholder: '-- Select Transaction --',
                allowClear: true,
                width: '100%'
            });

            // Optional: Submit otomatis saat pilih (hapus jika tidak diinginkan)
            $('#transactionSelect').on('change', function () {
                $('#compareForm').submit();
            });
        });

        // Auto submit form when selection changes
        document.getElementById('transactionSelect').addEventListener('change', function() {
            if (this.value) {
                document.getElementById('compareForm').submit();
            }
        });

        // Print styles for comparison
        const printStyles = `
            <style>
                @media print {
                    body { font-family: Arial, sans-serif; font-size: 12px; }
                    .no-print { display: none !important; }
                    .print-header { text-align: center; margin-bottom: 20px; }
                    .grid { display: flex; gap: 20px; }
                    .grid > div { flex: 1; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    th, td { border: 1px solid #ddd; padding: 6px; text-align: left; font-size: 11px; }
                    th { background-color: #f5f5f5; font-weight: bold; }
                    .comparison-header { background-color: #f0f0f0; padding: 10px; margin-bottom: 10px; }
                    .note-section { border: 2px solid #3B82F6; }
                    .transaction-section { border: 2px solid #10B981; }
                }
            </style>
        `;
        document.head.insertAdjacentHTML('beforeend', printStyles);
    </script>
@endsection
