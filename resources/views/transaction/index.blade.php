@extends('layouts.app')

@section('title', 'Transaction')

@if (session('success'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                toast: true,
                position: "bottom-end",
                icon: "success",
                title: "Transaction {{ session('success') }} successfully",
                showConfirmButton: false,
                width: "auto",
                timer: 2000,
                timerProgressBar: true,
                html: `<p class="text-[14px] font-light">Your transaction successfully {{ session('success') }}!</p>`,
                customClass: {
                    popup: 'py-3 px-4 rounded-[10px] ring-2 ring-[#82e095]/80 font-[inter]',
                    icon: "!mr-4",
                    title: "!p-0 !m-0 !mb-[2px] text-md font-semibold m-0 text-black",
                    htmlContainer: '!p-0 !m-0',
                    timerProgressBar: 'bg-[#f1f9f4]',
                }
            });
        });
    </script>
@endif

@section('content')
    <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">

        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">

            <!-- Search and Sorting -->
            <form method="GET" action="{{ route('transaction.index') }}" class="flex flex-col sm:flex-row gap-2 flex-wrap">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Invoice..."
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full sm:w-auto">
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition w-full sm:w-auto">
                    Search
                </button>
            </form>

            @if (Auth::guard('company')->check() == true || Auth::user()->can('create transaction'))
                <div class="flex justify-end md:justify-end">
                    <a href="{{ route('transaction.create') }}"
                        class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2 w-fit justify-center shadow">
                        Add Transaction
                    </a>
                </div>
            @endif

        </div>

        <div class="overflow-x-auto">
            <table class="w-full bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="px-4 py-3 text-center">No</th>
                        <th class="px-4 py-3 text-center">Voucher Code</th>
                        <th class="px-4 py-3 text-center">Customer Name</th>
                        <th class="px-4 py-3 text-center">Type</th>
                        <th class="px-4 py-3 text-center">Date</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>

                <tbody class="text-center">
                    @foreach ($transactions as $index => $transaction)
                        <tr class="border-gray-200 border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-3">{{ $index + $transactions->firstItem() }}</td>
                            <td class="px-4 py-3">{{ $transaction->voucher_code }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $transaction->customer_name }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-lg
                                                 {{ $transaction->type->name === 'Selling' ? 'bg-red-50 text-red-600 font-semibold' : ($transaction->type->name === 'Buying' ? 'bg-green-50 text-green-600' : 'bg-gray-300 text-gray-700') }}">
                                    {{ $transaction->type->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ \Carbon\Carbon::parse($transaction->date)->format('d-M-Y') }}
                            </td>
                            <td class="px-4 py-3 flex items-center justify-center">
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

                            <td class="px-4 py-3 text-center space-x-3">
                                <div class="flex gap-3 items-center justify-center">
                                    @if (Auth::guard('company')->check() == true || Auth::user()->can('view transaction'))
                                        <a href="{{ route('transaction.show', $transaction) }}">
                                            <div class="relative group">
                                                <div class="p-[5px] bg-green-50 rounded-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-5 text-green-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    </svg>
                                                </div>
                                                <span
                                                    class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md px-2 py-1">
                                                    View Transaction Detail
                                                </span>
                                            </div>
                                        </a>
                                    @endif

                                    @if (Auth::guard('company')->check() == true || Auth::user()->can('update transaction'))
                                        <a href="{{ route('transaction.edit', $transaction) }}">
                                            <div class="relative group">
                                                <div class="p-[5px] bg-blue-50 rounded-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-5 text-blue-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                    </svg>
                                                </div>
                                                <span
                                                    class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md px-2 py-1">
                                                    Edit Transaction
                                                </span>
                                            </div>
                                        </a>
                                    @endif

                                    @if (Auth::guard('company')->check() == true || Auth::user()->can('delete transaction'))
                                        <div class="relative group">
                                            <button
                                                onclick="confirmDeletePromo(event, '{{ route('transaction.destroy', $transaction->id) }}')"
                                                class="p-[5px] bg-red-50 rounded-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-red-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                            <span
                                                class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md px-2 py-1">
                                                Delete Transaction
                                            </span>
                                        </div>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $transactions->links('pagination::tailwind') }}
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
