@extends('layouts.app')
@section('title', 'Add Transaction')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">

        @if (session('success'))
            <div class="bg-green-500 text-white p-3 rounded-md mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('transaction.store') }}" method="POST" id="transaction">
            @csrf
            {{-- -- Customer Name --}}
            <div class="mb-4">
                <label for="customer_name" class="block text-gray-700 font-medium mb-2 capitalize">customer name</label>
                <input type="text" value="{{ old('customer_name') }}" name="customer_name" placeholder='customer name'
                    class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize">
                @error('customer_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- -- Type --}}
            <div class="mb-4">
                <label for="type" class="block text-gray-700 font-medium mb-2 capitalize">type</label>
                <select class="w-full bg-white px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize" name="type"
                    id="type">
                    <option disabled {{ old('type') ? '' : 'selected' }}>Select the Transaction Type</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}" {{ old('type') == $type->id ? 'selected' : '' }}>
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
                <select class="w-full bg-white px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize" name="payment"
                    id="payment">
                    <option disabled {{ old('payment') ? '' : 'selected' }}>Select the Transaction Payment</option>
                    @foreach ($payments as $payment)
                        <option value="{{ $payment->id }}">
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
                <input type="datetime-local" onfocus="this.showPicker()" value="{{ old('date') }}" name="date"
                    placeholder="date" class="w-full bg-white px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize">
                @error('date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>




            {{-- -- Status --}}
            <div class="mb-4">
                <label for="status" class="block text-gray-700 font-medium mb-2 capitalize">status</label>
                <select class="w-full bg-white px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize" name="status"
                    id="status">
                    <option disabled {{ old('status') ? '' : 'selected' }}>Select the Status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" class="capitalize"
                            {{ strtolower($transaction->status->name ?? '') == strtolower($status->name) ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>





            {{-- Transaction Detail Edit UI --}}
            <h1 class="text-xl font-semibold mb-3">Transaction Detail</h1>

            <div class="flex justify-end mb-4">
                <a href="javascript:void(0)" class="py-2 px-4 text-white bg-black rounded-md" id="add-transaction-btn">
                    Add Transaction Detail
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white shadow-md rounded-lg border border-gray-200" id="transaction_details">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                        <tr>
                            <th class="px-4 py-3 text-center">No</th>
                            <th class="px-4 py-3 text-center">Product</th>
                            <th class="px-4 py-3 text-center">Quantity</th>
                            <th class="px-4 py-3 text-center">Promo</th>
                            <th class="px-4 py-3 text-center">Price(Unit)</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        {{-- Example row --}}
                        {{-- 
            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                <td class="px-4 py-3">1</td>
                <td class="px-4 py-3">Product A</td>
                <td class="px-4 py-3">2</td>
                <td class="px-4 py-3">No</td>
                <td class="px-4 py-3">$10</td>
                <td class="px-4 py-3 space-x-2">
                    <button class="text-blue-600">Edit</button>
                    <button class="text-red-600">Delete</button>
                </td>
            </tr>
            --}}
                    </tbody>
                </table>
            </div>

            @error('transaction_details_json')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            {{-- -- Total Price --}}
            <div class="my-4">
                <label for="total_price" class="block text-gray-700 font-medium mb-2 capitalize">total price</label>
                <input value="{{ old('total_price') }}" type="text" name="total_price" id="total_price"
                    placeholder='total price' readonly
                    class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize">
                @error('total_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <input type="hidden" name="transaction_details_json" id="transaction_details_json" value="[]">

            <div class="flex justify-end space-x-2">
                <a href="{{ route('transaction.index') }}"
                    class="bg-red-500 text-white px-5 py-2 rounded-lg hover:bg-red-600 transition">
                    Cancel
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Add
                </button>
            </div>
        </form>

    </div>

    <script>
        document.getElementById('add-transaction-btn').addEventListener('click', function() {

            Swal.fire({
                title: 'Add Transaction Detail',
                html: `
        <div class="text-left">
             <div class="text-left">
            <div class="mb-4">
                <label for="product" class="block text-gray-700 font-medium mb-2 capitalize">Product</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize bg-white" id="product">
                    <option disabled selected>Select Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->name }}" 
                                data-sale-price="{{ $product->sale_price }}" 
                                data-purchase-price="{{ $product->purchase_price }}">
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="quantity" class="block text-gray-700 font-medium mb-2 capitalize">Quantity</label>
                <input placeholder="input quantity" type="number" id="quantity" class="placeholder:capitalize  border border-gray-300 rounded-md p-2 w-full" required>
            </div>


            <div class="mb-4">
                <label for="promo" class="block text-gray-700 font-medium mb-2 capitalize">Promo</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize bg-white" id="promo">
<option value="0" data-name="No Promo" selected>No Promo</option>
                    @foreach ($promos as $promo)
                        <option value="{{ $promo->amount }}" data-name="{{ $promo->name }}">{{ $promo->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                    <label for="price" class="block text-gray-700 font-medium mb-2 capitalize">Price</label>
                    <input type="number" id="price" class="border border-gray-300 rounded-md p-2 w-full" readonly>
                </div>
        </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const product = document.getElementById('product').value;
                    const quantity = document.getElementById('quantity').value;
                    const promoSelect = document.getElementById('promo');
                    const promo = promoSelect.value;
                    const promoName = promoSelect.options[promoSelect.selectedIndex].dataset.name;
                    const type = document.getElementById('type').value;

                    // Get the selected product option to retrieve the appropriate price
                    const selectedOption = document.getElementById('product').options[document
                        .getElementById('product').selectedIndex];
                    let price;

                    if (type === '2') {
                        price = selectedOption.dataset.purchasePrice; // Use purchase_price
                    } else if (type === '1') {
                        price = selectedOption.dataset.salePrice; // Use sale_price
                    } else {
                        price = document.getElementById('price')
                            .value; // Default value if type is neither 1 nor 2
                    }

                    if (!product || !quantity || !promo || !price) {
                        Swal.showValidationMessage('Please fill in all fields');
                        return false;
                    }
                    console.log(promo);
                    return {
                        product,
                        quantity,
                        promo,
                        promoName,
                        price
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    addTransactionRow(result.value);
                }
            });
            document.getElementById('product').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const type = document.getElementById('type').value;
                let salePrice = selectedOption.dataset.salePrice;
                let purchasePrice = selectedOption.dataset.purchasePrice;

                // Update price based on type
                if (type === '1') {
                    document.getElementById('price').value =
                        salePrice; // Set purchase price if type is 1
                } else if (type === '2') {
                    document.getElementById('price').value = purchasePrice; // Set sale price if type is 2
                }
            });
        });

        function checkTableEmpty() {
            const table = document.getElementById('transaction_details');
            const tbody = table.querySelector('tbody');
            const emptyMessage = document.getElementById('empty-message');

            if (tbody && tbody.rows.length === 0) {
                emptyMessage.classList.remove('hidden');
            } else {
                emptyMessage.classList.add('hidden');
            }
        }

        let transactionDetails = [];

        function updateTotalPrice() {
            let total = transactionDetails.reduce((sum, item) => {
                const qty = parseFloat(item.quantity) || 0;
                const price = parseFloat(item.price) || 0;
                const promoStr = item.promo?.toString() || "0";

                let itemTotal = qty * price;
                let discount = 0;

                // Only apply discount if promo is not "0" (No Promo)
                if (promoStr !== "0") {
                    const promoLength = promoStr.length;

                    if (promoLength > 3) {
                        // Fixed amount
                        discount = parseFloat(promoStr) || 0;
                    } else if (promoLength >= 1 && promoLength <= 2) {
                        // Percentage
                        const percent = parseFloat(promoStr) || 0;
                        discount = (itemTotal * percent) / 100;
                    }
                }

                return sum + (itemTotal - discount);
            }, 0);

            document.getElementById('total_price').value = total.toFixed(2);
        }


        function addTransactionRow(data) {
            const table = document.getElementById('transaction_details');
            const noDataRow = document.getElementById('no-data-row');

            if (noDataRow) {
                noDataRow.remove();
            }

            const rowCount = table.rows.length;

            // Add data to our transactions array
            transactionDetails.push({
                id: rowCount + 1,
                product: data.product,
                quantity: data.quantity,
                promo: data.promo,
                promoName: data.promoName,
                price: data.price
            });

            const row = table.insertRow();
            row.classList.add('border-gray-200', 'bg-white', 'border-b', 'hover:bg-gray-50', 'transition', 'text-center');
            row.dataset.rowId = rowCount + 1; // Store row ID for reference

            row.innerHTML = `
        <td class="px-4 py-3 text-center">${rowCount}</td>
        <td class="px-4 py-3 text-center">${data.product}</td>
        <td class="px-4 py-3 text-center">${data.quantity}</td>
        <td class="px-4 py-3 text-center">${data.promoName}</td>
        <td class="px-4 py-3 text-center">${data.price}</td>
        <td class="px-4 py-3 text-center">
            <button class="edit-btn text-blue-500 hover:underline" data-id="${rowCount + 1}">Edit</button> |
            <button class="delete-btn text-red-500 hover:underline" data-id="${rowCount + 1}">Delete</button>
        </td>
    `;

            row.querySelector('.edit-btn').addEventListener('click', function(e) {
                e.preventDefault();
                editTransactionRow(row);
            });

            row.querySelector('.delete-btn').addEventListener('click', function() {
                deleteTransactionRow(row);
            });

            const emptyMessage = document.getElementById('empty-message');
            if (emptyMessage) {
                emptyMessage.classList.add('hidden');
            }

            updateTotalPrice();
            console.log(transactionDetails);
        }

        function deleteTransactionRow(row) {
            const rowId = parseInt(row.dataset.rowId);

            // Remove from our transactions array
            transactionDetails = transactionDetails.filter(item => item.id !== rowId);

            row.remove();
            const table = document.getElementById('transaction_details');

            // If all rows are deleted, show the empty message
            if (table.rows.length <= 1) { // Accounting for header row
                const tbody = table.querySelector('tbody');
                if (tbody) {
                    tbody.innerHTML = `
                <tr id="no-data-row">
                    <td class="px-4 py-3 text-center text-gray-500" colspan="6">Please insert the transaction detail</td>
                </tr>
            `;
                }

            }
            // Update the total price
            updateTotalPrice();
        }

        function editTransactionRow(row) {
            const rowId = parseInt(row.dataset.rowId);
            const item = transactionDetails.find(item => item.id === rowId);

            if (!item) return;

            Swal.fire({
                title: 'Edit Transaction',
                html: `
        <div class="text-left">
            <div class="mb-4">
                <label for="edit-product" class="block text-gray-700 font-medium mb-2 capitalize">Product</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize bg-white" id="edit-product">
                    <option value="${item.product}" selected>${item.product}</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->name }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="edit-quantity" class="block text-gray-700 font-medium mb-2 capitalize">Quantity</label>
                <input type="number" id="edit-quantity" class=" border border-gray-300 rounded-md p-2 w-full" value="${item.quantity}">
            </div>

            <div class="mb-4">
                <label for="edit-promo" class="block text-gray-700 font-medium mb-2 capitalize ">Promo</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize bg-white" id="edit-promo">
                    <option value="${item.promo}" data-name="${item.promoName}" selected>${item.promoName}</option>
                    <option value="0" data-name="No Promo" ${item.promo === "0" ? 'selected' : ''}>No Promo</option>
                    @foreach ($promos as $promo)
                        <option value="{{ $promo->amount }}" data-name="{{ $promo->name }}">{{ $promo->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="edit-price" class="block text-gray-700 font-medium mb-2 capitalize">Price</label>
                <input type="number" id="edit-price" class=" border border-gray-300 rounded-md p-2 w-full" value="${item.price}">
            </div>
        </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const product = document.getElementById('edit-product').value;
                    const quantity = document.getElementById('edit-quantity').value;
                    const promoSelect = document.getElementById('edit-promo');
                    const promo = promoSelect.value;
                    const promoName = promoSelect.options[promoSelect.selectedIndex].dataset.name;
                    const price = document.getElementById('edit-price').value;

                    if (!product || !quantity || !price) {
                        Swal.showValidationMessage('Please fill in all required fields');
                        return false;
                    }

                    return {
                        product,
                        quantity,
                        promo,
                        promoName,
                        price
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Update the transaction details array
                    const updatedItem = transactionDetails.find(item => item.id === rowId);
                    if (updatedItem) {
                        updatedItem.product = result.value.product;
                        updatedItem.quantity = result.value.quantity;
                        updatedItem.promo = result.value.promo;
                        updatedItem.promoName = result.value.promoName;
                        updatedItem.price = result.value.price;
                    }

                    // Update the table row
                    const cells = row.getElementsByTagName('td');
                    cells[1].textContent = result.value.product;
                    cells[2].textContent = result.value.quantity;
                    cells[3].textContent = result.value.promoName;
                    cells[4].textContent = result.value.price;

                    // Update the total price
                    updateTotalPrice();
                    console.log(transactionDetails);
                }
            });
        }
        // Function untuk inisialisasi tabel kosong
        function initializeEmptyTable() {
            const table = document.getElementById('transaction_details');
            const tbody = table.querySelector('tbody');

            if (tbody && tbody.rows.length === 0) {
                tbody.innerHTML = `
            <tr id="no-data-row">
                <td class="px-4 py-3 text-center text-gray-500" colspan="6">Please insert the transaction detail</td>
            </tr>
        `;

                const emptyMessage = document.getElementById('empty-message');
                if (emptyMessage) {
                    emptyMessage.classList.remove('hidden');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('#transaction').addEventListener('submit', function(e) {

                const transactionDetailsInput = document.querySelector('#transaction_details_json');
                transactionDetailsInput.value = JSON.stringify(transactionDetails);
            });

            const form = document.querySelector('form');

            initializeEmptyTable();

            if (document.getElementById('total_price')) {
                updateTotalPrice();
            }
        });
    </script>
@endsection
