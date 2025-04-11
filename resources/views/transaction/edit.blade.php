@extends('layouts.app')
@section('title', 'Edit Transaction')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">

        @if (session('success'))
            <div class="bg-green-500 text-white p-3 rounded-md mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('transaction.update', $transaction->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{--Transaction Edit--}}

            {{-- -- Customer Name--}}
            <div class="mb-4">
                <label for="customer_name" class="block text-gray-700 font-medium mb-2 capitalize">customer name</label>
                <input type="text"
                       name="customer_name" placeholder='customer name'
                       value="{{ old('customer_name', $transaction->customer_name) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize">
                       @error('customer_name')
                       <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                   @enderror
            </div>

            {{-- -- Type --}}
            <div class="mb-4">
                <label for="type" class="block text-gray-700 font-medium mb-2 capitalize">type</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize" name="type" id="type">
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}" {{ $transaction->type_id == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- -- Payment --}}
            <div class="mb-4">
                <label for="payment" class="block text-gray-700 font-medium mb-2 capitalize">payment</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize" name="payment" id="payment">
                    @foreach ($payments as $payment)
                        <option value="{{ $payment->id }}" {{ $transaction->payment_id == $payment->id ? 'selected' : '' }}>
                                {{ $payment->name }}
                        </option>
                    @endforeach
                </select>
                @error('payment')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- -- Date --}}
            <div class="mb-4">
                <label for="date" class="block text-gray-700 font-medium mb-2 capitalize">date</label>
                <input type="datetime-local"
                       onfocus="this.showPicker()"
                       name="date" placeholder='date'
                       value="{{ old('date', $transaction->date) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize">
                       @error('date')
                       <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                   @enderror
                @error('date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- -- Status --}}
            <div class="mb-4">
                <label for="status" class="block text-gray-700 font-medium mb-2 capitalize">status</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize" name="status" id="status">
                    <option disabled>Select the Status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}"
                                class="capitalize"
                                {{ strtolower($transaction->status->name ?? '') == strtolower($status->name) ? 'selected' : '' }}>
                {{ $status->name }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

        {{--Transaction Detail Edit UI--}}
        <h1 class="text-xl font-semibold mb-3">Transaction Detail</h1>
        <table class="w-full bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
            <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                <tr>
                    <th class="px-4 py-3 text-center">No</th>
                    <th class="px-4 py-3 text-center">Product</th>
                    <th class="px-4 py-3 text-center">Quantity</th>
                    <th class="px-4 py-3 text-center">Promo</th>
                    <th class="px-4 py-3 text-center">Price</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
            </thead>

            <tbody class="text-center">
                @foreach($transaction->transactionDetails as $index => $detail)
                    <tr class="border-gray-200 border-b hover:bg-gray-50 transition">
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">{{ $detail->product->name }}</td>
                        <td class="px-4 py-3">{{ $detail->quantity }}</td>
                        <td class="px-4 py-3">{{ $detail->promo ? $detail->promo->name : 'No Promo' }}</td>
                        <td class="px-4 py-3">{{ $detail->price }}</td>

                        <td class="px-4 py-3 text-center space-x-3">
                            <div class="flex gap-3 items-center justify-center">
                                @if (Auth::guard('company')->check() == true || Auth::user()->can('update transaction'))
                                    <div class="relative group">
                                        <button
                                            type="button"
                                            onclick="editTransactionDetail({{ $detail->id }}, '{{ $detail->product->name }}', {{ $detail->quantity }}, {{ $detail->promo ? $detail->promo->id : 'null' }}, {{ $detail->price }})"
                                            class="p-[5px] bg-blue-50 rounded-md">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-blue-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md px-2 py-1">
                                            Edit Transaction Detail
                                        </span>
                                    </div>
                                @endif

                                @if (Auth::guard('company')->check() == true || Auth::user()->can('delete transactionDetails'))
                                    <div class="relative group">
                                        <button
                                                type="button"
                                                onclick="confirmDeletePromo(event, '{{ route('transactionDetails.destroy', $detail->id) }}')"
                                                class="p-[5px] bg-red-50 rounded-md"
                                                >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-red-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                        </button>
                                        <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md px-2 py-1">
                                            Delete Transaction Detail
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- -- Total Price --}}
        <div class="my-4">
            <label for="total_price" class="block text-gray-700 font-medium mb-2 capitalize">total price</label>
            <input
                    type="text"
                    readonly
                    name="total_price" placeholder='customer name'
                    value="{{ old('total_price', $transaction->total_price) }}"
                    class="cursor-not-allowed w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize">
                    @error('total_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                @error('total_price')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end space-x-2 my-5">
            <a href="{{ route('transaction.index') }}"
               class="bg-red-500 text-white px-5 py-2 rounded-lg hover:bg-red-600 transition">
                Cancel
            </a>
            <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                Update
            </button>
        </div>
        </form>
    </div>

    <script>

        function dd(...args) {
            console.log(...args);
            debugger; // Ini akan pause eksekusi kalau DevTools dibuka
            throw new Error("dd() - Execution stopped");
        }
        // Delete Transaction Detail
        function confirmDeletePromo(event, deleteUrl) {
            event.preventDefault();
            Swal.fire({
                title: `<h1 class="font-bold">Are you sure want to<br/> delete this transaction detail?</h1>`,
                showCancelButton: true,
                imageUrl: "https://cdn-icons-png.flaticon.com/512/3300/3300464.png",
                imageWidth: 100,
                imageHeight: 100,
                html: `<p class="text-[.98rem]">You won't be able to revert this!</p>`,
                imageAlt: "Delete Icon",
                confirmButtonColor: "#d33",
                reverseButtons: true,
                customClass: {
                    popup: "rounded-2xl max-w-md",
                    cancelButton:
                    "bg-white text-gray-700 hover:text-white hover:bg-gray-400 transition-all duration-150 ring-2 ring-[#eaeaea] py-[4px] px-12",
                    confirmButton: "py-[6px] px-12 rounded-md",
                    title: "p-0",
                },
                confirmButtonText: "Delete",
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('_method', 'DELETE');
                    formData.append('_token', '{{ csrf_token() }}');
                    fetch(deleteUrl, {
                        method: 'POST',
                        body: formData,
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            console.log("Data yang dikirim ke backend:", data); // tampil di console

                            if (data.success) {
                                const totalPriceInput = document.querySelector('input[name="total_price"]');
                                if (data.total_price !== undefined && totalPriceInput) {
                                    totalPriceInput.value = data.total_price;
                                }

                                sessionStorage.setItem('transactionDetailSuccess', true);
                                location.reload();
                            } else {
                                // Show the error alert with backend message
                                Swal.fire({
                                    icon: "error",
                                    title: "Delete Failed",
                                    html: `<p class="text-sm text-gray-700">${data.message}</p>`,
                                    confirmButtonColor: "#d33",
                                    customClass: {
                                        popup: "rounded-xl max-w-sm",
                                    },
                                });
                            }
                        })
                        .catch((error) => {
                            console.error("Delete error:", error);
                            Swal.fire({
                                icon: "error",
                                title: "Delete Failed",
                                html: `<p class="text-sm text-gray-700">${error.message}</p>`,
                                confirmButtonColor: "#d33",
                                customClass: {
                                    popup: "rounded-xl max-w-sm",
                                },
                            });
                        });
                }
            });
        }

        // Edit Transaction Detail
        function editTransactionDetail(detailId, productName, quantity, promoId, price) {
            const promoOptions = `
        <option value="">No Promo</option>
        @foreach($promos as $promo)
            <option value="{{ $promo->id }}" ${promoId == {{ $promo->id }} ? 'selected' : ''}>
                {{ $promo->name }}
            </option>
        @endforeach
    `;

                Swal.fire({
                    title: '<h2 class="font-bold text-lg">Edit Transaction Detail</h2>',
                    html: `
            <input type="hidden" id="detailId" value="${detailId}">

            <div class="mb-4 text-left">
                <label class="block text-gray-700 font-medium mb-1">Product Name</label>
                <input type="text" id="productName" value="${productName}" readonly class="cursor-not-allowed w-full px-4 py-2 border border-gray-300 rounded-md">
            </div>

            <div class="mb-4 text-left">
                <label class="block text-gray-700 font-medium mb-1">Quantity</label>
                <input type="number" id="quantity" value="${quantity}" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-md">
            </div>

            <div class="mb-4 text-left">
                <label class="block text-gray-700 font-medium mb-1">Promo</label>
                <select id="promo" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                    ${promoOptions}
                </select>
            </div>

            <div class="mb-4 text-left">
                <label class="block text-gray-700 font-medium mb-1">Price</label>
                <input type="number" id="price" value="${price}" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-md">
            </div>
        `,
                    showCancelButton: true,
                    confirmButtonText: "Update",
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    reverseButtons: true,
                    customClass: {
                        popup: "rounded-xl max-w-md",
                        confirmButton: "!py-2 !px-6 !rounded-md !bg-[#4338ca]",
                        cancelButton: "!py-2 !px-6  !text-gray-700 !bg-white !hover:text-white !hover:bg-gray-400 !transition-all !duration-150 !ring-2 !ring-[#eaeaea] !rounded-md",
                    },
                    preConfirm: () => {
                        const quantity = +document.getElementById('quantity').value;
                        const price = +document.getElementById('price').value;
                        const promoId = document.getElementById('promo').value;

                        if (quantity <= 0) return Swal.showValidationMessage('Quantity must be greater than 0');
                        if (price < 0) return Swal.showValidationMessage('Price cannot be negative');

                        return { detailId, quantity, promoId, price };
                    }
                }).then(({ isConfirmed, value }) => {
                    if (!isConfirmed) return;

                    const formData = new FormData();
                    formData.append('_method', 'PUT');
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('quantity', value.quantity);
                    formData.append('promo_id', value.promoId || '');
                    formData.append('price', value.price);

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    fetch(`/transactionDetails/${value.detailId}`, {
                        method: 'PUT',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            _method: 'PUT',
                            _token: '{{ csrf_token() }}',
                            quantity: value.quantity,
                            promo_id: value.promoId || '',
                            price: value.price
                        }),
                    })

                        .then(async (response) => {
                            let data = {};
                            try {
                                data = await response.json();
                            } catch (error) {
                                console.warn('Response bukan JSON, menganggap sukses:', error);
                                data = { success: true };
                            }

                            console.log("Data yang dikirim ke backend:", data);

                            const totalPriceInput = document.querySelector('input[name="total_price"]');
                            if (data.total_price !== undefined && totalPriceInput) {
                                totalPriceInput.value = data.total_price;
                            }

                            sessionStorage.setItem('transactionDetailSuccess', true);
                            location.reload();
                        });
                });
            }

        // Success toast for deleted transactions
        window.addEventListener("DOMContentLoaded", () => {
            if (sessionStorage.getItem('transactionDetailSuccess')) {
                // Clear the flag
                sessionStorage.removeItem('transactionDetailSuccess');

                // Show the success alert
                Swal.fire({
                    toast: true,
                    position: "bottom-end",
                    icon: "success",
                    title: "Detail updated successfully!",
                    html: `<p class="text-[14px] font-light">Transaction detail has been updated</p>`,
                    showConfirmButton: false,
                    width: "400px",
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'py-3 px-4 rounded-[10px] ring-2 ring-[#82e095]/80 font-[inter]',
                        icon: "!mr-4",
                        title: "!p-0 !m-0 !mb-[2px] text-md font-semibold m-0 text-black",
                        htmlContainer: '!p-0 !m-0',
                        timerProgressBar: 'bg-[#f1f9f4]',
                    }
                });
            }

            if (sessionStorage.getItem("deleted") === "true") {
                sessionStorage.removeItem("deleted");
                Swal.fire({
                    toast: true,
                    position: "bottom-end",
                    icon: "success",
                    title: "Transaction detail deleted successfully!",
                    html: `<p class="text-[14px] font-light">Your transaction detail has been deleted</p>`,
                    showConfirmButton: false,
                    width: "400px",
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'py-3 px-4 rounded-[10px] ring-2 ring-[#82e095]/80 font-[inter]',
                        icon: "!mr-4",
                        title: "!p-0 !m-0 !mb-[2px] text-md font-semibold m-0 text-black",
                        htmlContainer: '!p-0 !m-0',
                        timerProgressBar: 'bg-[#f1f9f4]',
                    }
                });
            }
        });
    </script>
@endsection
