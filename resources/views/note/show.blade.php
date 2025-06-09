@extends('layouts.app')
@section('title', 'Note Detail')

@section('content')
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">

        @if (session('success'))
            <div class="bg-green-500 text-white p-3 rounded-md mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-green-500 text-white p-3 rounded-md mb-4">
                {{ session('error') }}
            </div>
        @endif
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Note Detail</h1>
            <div class="flex space-x-2">
                {{-- <a href="{{ route('note.edit', $note->id) }}" --}}
                {{--    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition"> --}}
                {{--     Edit --}}
                {{-- </a> --}}
                <a href="{{ route('note.index') }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                    Back to List
                </a>
                <a href="{{ route('note.download-pdf', $note->id) }}"
                   class="bg-red-500 text-white px-5 py-2 rounded-lg hover:bg-red-600 transition">
                   <i class="fas fa-file-pdf mr-2"></i>Download PDF
                </a>
            </div>
        </div>

        {{-- Note Information --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-50 p-6 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Note Information</h2>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Customer Name</label>
                        <p class="text-gray-800 font-medium">{{ $note->customer_name }}</p>
                    </div>

                    {{-- <div> --}}
                    {{--     <label class="block text-sm font-medium text-gray-600">Voucher Code</label> --}}
                    {{--     <p class="text-gray-800 font-medium">{{ $note->voucher_code }}</p> --}}
                    {{-- </div> --}}

                    <div>
                        <label class="block text-sm font-medium text-gray-600">Total Price</label>
                        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($note->total_price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Additional Information</h2>

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Description</label>
                        <p class="text-gray-800">{{ $note->description }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600">Created At</label>
                        <p class="text-gray-800">{{ $note->created_at->format('d M Y H:i:s') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600">Last Updated</label>
                        <p class="text-gray-800">{{ $note->updated_at->format('d M Y H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Note Details Table --}}
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Note Details</h2>

            @if($note->noteDetails && $note->noteDetails->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                            <tr>
                                <th class="px-4 py-3 text-center">No</th>
                                <th class="px-4 py-3 text-left">Product Name</th>
                                <th class="px-4 py-3 text-center">Quantity</th>
                                <th class="px-4 py-3 text-center">Unit Price</th>
                                <th class="px-4 py-3 text-center">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($note->noteDetails as $index => $detail)
                                <tr class="border-gray-200 border-b hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-800">{{ $detail->product->name ?? 'Product not found' }}</div>
                                        @if($detail->product && $detail->product->description)
                                            <div class="text-sm text-gray-500">{{ $detail->product->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm">
                                            {{ number_format($detail->quantity, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        Rp {{ number_format($detail->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-medium">
                                        Rp {{ number_format($detail->quantity * $detail->price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-semibold text-gray-700">
                                    Total Amount:
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-lg text-blue-600">
                                    Rp {{ number_format($note->total_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="bg-gray-100 border border-gray-300 rounded-lg p-8 text-center">
                    <div class="text-gray-500 text-lg mb-2">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                    </div>
                    <p class="text-gray-600 font-medium">No note details found</p>
                    <p class="text-gray-500 text-sm">This note doesn't have any detailed items.</p>
                </div>
            @endif
        </div>

        {{-- Related Transaction Info --}}
        {{-- @if($note->transaction) --}}
        {{--     <div class="bg-blue-50 border border-blue-200 rounded-lg p-6"> --}}
        {{--         <h3 class="text-lg font-semibold text-blue-800 mb-3">Related Transaction</h3> --}}
        {{--         <div class="grid grid-cols-1 md:grid-cols-3 gap-4"> --}}
        {{--             <div> --}}
        {{--                 <label class="block text-sm font-medium text-blue-600">Transaction Customer</label> --}}
        {{--                 <p class="text-blue-800 font-medium">{{ $note->transaction->customer_name }}</p> --}}
        {{--             </div> --}}
        {{--             <div> --}}
        {{--                 <label class="block text-sm font-medium text-blue-600">Transaction Date</label> --}}
        {{--                 <p class="text-blue-800 font-medium">{{ $note->transaction->date ? \Carbon\Carbon::parse($note->transaction->date)->format('d M Y H:i') : 'N/A' }}</p> --}}
        {{--             </div> --}}
        {{--             <div> --}}
        {{--                 <label class="block text-sm font-medium text-blue-600">Transaction Total</label> --}}
        {{--                 <p class="text-blue-800 font-medium">Rp {{ number_format($note->transaction->total_price, 0, ',', '.') }}</p> --}}
        {{--             </div> --}}
        {{--         </div> --}}
        {{--         <div class="mt-3"> --}}
        {{--             <a href="{{ route('transaction.show', $note->transaction->id) }}" --}}
        {{--                class="text-blue-600 hover:text-blue-800 font-medium text-sm hover:underline"> --}}
        {{--                 View Transaction Details → --}}
        {{--             </a> --}}
        {{--         </div> --}}
        {{--     </div> --}}
        {{-- @endif --}}

    </div>

    <script>
        {{-- function deleteNote(noteId) { --}}
        {{--     Swal.fire({ --}}
        {{--         title: 'Are you sure?', --}}
        {{--         text: "You won't be able to revert this!", --}}
        {{--         icon: 'warning', --}}
        {{--         showCancelButton: true, --}}
        {{--         confirmButtonColor: '#d33', --}}
        {{--         cancelButtonColor: '#3085d6', --}}
        {{--         confirmButtonText: 'Yes, delete it!' --}}
        {{--     }).then((result) => { --}}
        {{--         if (result.isConfirmed) { --}}
        {{--             // Send delete request --}}
        {{--             fetch(`{{ route('note.destroy', '') }}/${noteId}`, { --}}
        {{--                 method: 'DELETE', --}}
        {{--                 headers: { --}}
        {{--                     'X-CSRF-TOKEN': '{{ csrf_token() }}', --}}
        {{--                     'Content-Type': 'application/json', --}}
        {{--                 }, --}}
        {{--             }) --}}
        {{--             .then(response => response.json()) --}}
        {{--             .then(data => { --}}
        {{--                 if (data.success) { --}}
        {{--                     Swal.fire( --}}
        {{--                         'Deleted!', --}}
        {{--                         data.message, --}}
        {{--                         'success' --}}
        {{--                     ).then(() => { --}}
        {{--                         window.location.href = '{{ route('note.index') }}'; --}}
        {{--                     }); --}}
        {{--                 } else { --}}
        {{--                     Swal.fire( --}}
        {{--                         'Error!', --}}
        {{--                         data.message, --}}
        {{--                         'error' --}}
        {{--                     ); --}}
        {{--                 } --}}
        {{--             }) --}}
        {{--             .catch(error => { --}}
        {{--                 Swal.fire( --}}
        {{--                     'Error!', --}}
        {{--                     'Something went wrong!', --}}
        {{--                     'error' --}}
        {{--                 ); --}}
        {{--             }); --}}
        {{--         } --}}
        {{--     }); --}}
        {{-- } --}}

        // Print styles
        const printStyles = `
            <style>
                @media print {
                    body { font-family: Arial, sans-serif; }
                    .no-print { display: none !important; }
                    .print-header { text-align: center; margin-bottom: 20px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f5f5f5; }
                }
            </style>
        `;
        document.head.insertAdjacentHTML('beforeend', printStyles);
    </script>
@endsection
