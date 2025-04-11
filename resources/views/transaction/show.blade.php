@extends('layouts.app')

@section('title', 'Transaction Detail')

@section('content')
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">
        <div class="flex justify-between items-center">
            <h1 class="text-xl font-semibold mb-3">{{$transaction->customer_name}}'s Transaction Detail </h1>
            <h1 class="font-semibold mb-3 bg-gray-50 px-4 py-2 rounded-md">{{$transaction->voucher_code}}</h1>
        </div>
        <div class="flex justify-between items-center">
            <a href="{{ route('transaction.index') }}" class="text-sm text-blue-500 underline">Back to Transactions</a>
            <button class="bg-stone-600 hover:bg-black transition-all duration-150 text-white py-1 px-4 rounded-lg">
                Export PDF
            </button>
        </div>
        {{-- <div class="flex justify-between mb-6 items-center"> --}}
            {{----}}
            {{--     <!-- Search and Sorting --> --}}
            {{--     <form method="GET" action="{{ route('transaction.index') }}" class="flex gap-2 flex-wrap"> --}}
                {{--         <input type="text" name="search" value="{{ request('search') }}" placeholder="Search promo..." --}}
                {{--             class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"> --}}
                {{----}}
                {{--         <div class="relative w-38"> --}}
                    {{--             <select name="sort" id="sort" onchange="this.form.submit()" --}}
                        {{--                 class="appearance-none w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg bg-white text-gray-700 font-medium text-[15px] focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm"> --}}
                        {{--                 <option value="" disabled>Sort by</option> --}}
                        {{--                 <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>A - Z</option> --}}
                        {{--                 <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Z - A</option> --}}
                        {{--             </select> --}}
                    {{--             <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none"> --}}
                        {{--                 <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" --}}
                            {{--                     fill="currentColor"> --}}
                            {{--                     <path fill-rule="evenodd" --}}
                            {{--                         d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" --}}
                            {{--                         clip-rule="evenodd" /> --}}
                            {{--                 </svg> --}}
                        {{--             </div> --}}
                    {{--         </div> --}}
                {{----}}
                {{--     </form> --}}
            {{----}}
            {{--     @if (Auth::guard('company')->check() == true || Auth::user()->can('create promo')) --}}
                {{--         <a href="{{ route('transaction.create') }}" --}}
                    {{--             class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2"> --}}
                    {{--             Add Transaction --}}
                    {{--         </a> --}}
                {{--     @endif --}}
                {{----}}
                {{-- </div> --}}

            <div class="overflow-x-auto mt-3">
                <table class="w-full bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                        <tr>
                            <th class="px-4 py-3 text-center">No</th>
                            <th class="px-4 py-3 text-center">Product</th>
                            <th class="px-4 py-3 text-center">Quantity</th>
                            <th class="px-4 py-3 text-center">Promo</th>
                            <th class="px-4 py-3 text-center">Price</th>
                        </tr>
                    </thead>

                    <tbody class="text-center">
                        @foreach($transaction->transactionDetails as $index => $detail)
                            <tr class="border-gray-200 border-b hover:bg-gray-50 transition">
                                <td class="px-4 py-3">{{ $index + 1 }}</td>
                                <td>{{ $detail->product ? $detail->product->name : 'Product Not Found' }}</td>
                                <td class="px-4 py-3">{{ $detail->quantity }}</td>
                                <td class="px-4 py-3">{{ $detail->promo ? $detail->promo->name : 'No Promo' }}</td>
                                <td class="px-4 py-3">{{ number_format($detail->price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    <!-- Grand Total Row -->
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right font-bold">Total Price:</td>
                            <td class="px-4 py-2 text-center font-bold text-lg">{{ $transaction->total_price}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{-- {{ $transactions->links('pagination::tailwind') }} --}}
        </div>
    </div>

    <script>
        function confirmDeletePromo(event, deleteUrl) {
            event.preventDefault();
            Swal.fire({
                title: `<h1 class="font-bold">Are you sure want to<br/> delete this transaction?</h1>`,
                showCancelButton: true,
                imageUrl: "https://cdn-icons-png.flaticon.com/512/3300/3300464.png",
                imageWidth: 100,
                imageHeight: 100,
                html: `<p class="text-[.98rem]">You won't be able to revert this!</p>`,
                imageAlt: "Delete Icon",
                confirmButtonColor: "#d33",
                reverseButtons: 'true',
                customClass: {
                    popup: "rounded-2xl max-w-md",
                    cancelButton: "bg-white text-gray-700 hover:text-white hover:bg-gray-400 transition-all duration-150 ring-2 ring-[#eaeaea] py-[4px] px-12",
                    confirmButton: "py-[6px] px-12 rounded-md",
                    title: "p-0",
                },
                confirmButtonText: "Delete"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(deleteUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'DELETE'
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                sessionStorage.setItem("deleted", "true");
                                location.reload();
                            } else {
                                throw new Error("Failed to delete promo");
                            }
                        })
                        .catch(() => {
                            Swal.fire({
                                toast: true,
                                position: "top-end",
                                icon: "error",
                                title: "Failed to delete promo!",
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });
                        });
                }
            });
        }

        // Cek setelah reload apakah ada status "deleted"
        window.addEventListener("DOMContentLoaded", () => {
            if (sessionStorage.getItem("deleted") === "true") {
                sessionStorage.removeItem("deleted"); // Hapus status setelah ditampilkan
                Swal.fire({
                    toast: true,
                    position: "bottom-end",
                    icon: "success",
                    title: "Transaction deleted successfully!",
                    html: `<p class="text-[14px] font-light">Your transaction has been deleted</p>`,
                    showConfirmButton: false,
                    width: "400px", // Atur lebar toast
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'py-3 px-4 rounded-[10px] ring-2 ring-[#82e095]/80 font-[inter]',
                        icon: "!mr-4", // Tambahkan margin ke ikon
                        title: "!p-0 !m-0 !mb-[2px] text-md font-semibold m-0 text-black",
                        htmlContainer: '!p-0 !m-0',
                        timerProgressBar: 'bg-[#f1f9f4]',
                    }

                });
            }
        });
    </script>
@endsection
