@extends('layouts.app')
@section('title', 'Add Note')

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

        <form action="{{ route('note.store') }}" method="POST" id="note">
            @csrf
            {{-- Customer Name --}}
            <div class="mb-4">
                <label for="customer_name" class="block text-gray-700 font-medium mb-2 capitalize">customer name</label>
                <input type="text" value="{{ old('customer_name') }}" name="customer_name" placeholder='customer name'
                    class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize">
                @error('customer_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-medium mb-2 capitalize">description</label>
                <textarea name="description" id="description" rows="3" placeholder="description"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Voucher Code --}}
            {{-- <div class="mb-4"> --}}
            {{--     <label for="voucher_code" class="block text-gray-700 font-medium mb-2 capitalize">voucher code</label> --}}
            {{--     <select class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize" name="voucher_code" --}}
            {{--         id="voucher_code"> --}}
            {{--         <option disabled {{ old('voucher_code') ? '' : 'selected' }}>Select Voucher Code</option> --}}
            {{--         @foreach ($transactions as $transaction) --}}
            {{--             <option value="{{ $transaction->voucher_code }}" {{ old('voucher_code') == $transaction->voucher_code ? 'selected' : '' }}> --}}
            {{--                 {{ $transaction->voucher_code }} - {{ $transaction->customer_name }} --}}
            {{--             </option> --}}
            {{--         @endforeach --}}
            {{--     </select> --}}
            {{--     @error('voucher_code') --}}
            {{--         <p class="text-red-500 text-sm mt-1">{{ $message }}</p> --}}
            {{--     @enderror --}}
            {{-- </div> --}}

            {{-- Note Detail Edit UI --}}
            <h1 class="text-xl font-semibold mb-3">Note Detail</h1>
            <table class="w-full bg-white shadow-md rounded-lg overflow-hidden border border-gray-200"
                id="note_details">
                <div class="flex justify-end mb-3">
                    <a href="javascript:void(0)" class="py-2 px-4 text-white bg-black rounded-md mt-4 mr-4"
                        id="add-note-btn">Add Note Detail</a>
                </div>
                <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="px-4 py-3 text-center">No</th>
                        <th class="px-4 py-3 text-center">Product</th>
                        <th class="px-4 py-3 text-center">Quantity</th>
                        <th class="px-4 py-3 text-center">Price(Unit)</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="text-center">
                    {{-- Dynamic content will be added here --}}
                </tbody>
            </table>
            @error('note_details_json')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            {{-- Total Price --}}
            <div class="my-4">
                <label for="total_price" class="block text-gray-700 font-medium mb-2 capitalize">total price</label>
                <input value="{{ old('total_price') }}" type="text" name="total_price" id="total_price"
                    placeholder='total price' readonly
                    class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize">
                @error('total_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <input type="hidden" name="note_details_json" id="note_details_json" value="[]">

            <div class="flex justify-end space-x-2">
                <a href="{{ route('note.index') }}"
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
        document.getElementById('add-note-btn').addEventListener('click', function() {
            Swal.fire({
                title: 'Add Note Detail',
                html: `
        <div class="text-left">
            <div class="mb-4">
                <label for="product" class="block text-gray-700 font-medium mb-2 capitalize">Product</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize" id="product">
                    <option disabled selected>Select Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-purchase-price="{{ $product->purchase_price }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="quantity" class="block text-gray-700 font-medium mb-2 capitalize">Quantity</label>
                <input placeholder="input quantity" type="number" id="quantity" class="placeholder:capitalize border border-gray-300 rounded-md p-2 w-full" required>
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
                    const productSelect = document.getElementById('product');
                    const productId = productSelect.value;
                    const productName = productSelect.options[productSelect.selectedIndex].dataset.name;
                    const quantity = document.getElementById('quantity').value;
                    const price = document.getElementById('price').value;

                    if (!productId || !quantity || !price) {
                        Swal.showValidationMessage('Please fill in all fields');
                        return false;
                    }

                    return {
                        productId,
                        productName,
                        quantity,
                        price
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    addNoteRow(result.value);
                }
            });

            // Event listener for product selection
            document.getElementById('product').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const purchasePrice = selectedOption.dataset.purchasePrice;
                document.getElementById('price').value = purchasePrice;
            });
        });

        let noteDetails = [];

        function updateTotalPrice() {
            let total = noteDetails.reduce((sum, item) => {
                const qty = parseFloat(item.quantity) || 0;
                const price = parseFloat(item.price) || 0;
                return sum + (qty * price);
            }, 0);

            document.getElementById('total_price').value = total.toFixed(2);
        }

        function addNoteRow(data) {
            const table = document.getElementById('note_details');
            const noDataRow = document.getElementById('no-data-row');

            if (noDataRow) {
                noDataRow.remove();
            }

            const rowCount = table.rows.length;

            // Add data to our notes array
            noteDetails.push({
                id: rowCount + 1,
                productId: data.productId,
                productName: data.productName,
                quantity: data.quantity,
                price: data.price
            });

            const row = table.insertRow();
            row.classList.add('border-gray-200', 'bg-white', 'border-b', 'hover:bg-gray-50', 'transition', 'text-center');
            row.dataset.rowId = rowCount + 1;

            row.innerHTML = `
        <td class="px-4 py-3 text-center">${rowCount}</td>
        <td class="px-4 py-3 text-center">${data.productName}</td>
        <td class="px-4 py-3 text-center">${data.quantity}</td>
        <td class="px-4 py-3 text-center">${data.price}</td>
        <td class="px-4 py-3 text-center">
            <button class="edit-btn text-blue-500 hover:underline" data-id="${rowCount + 1}">Edit</button> |
            <button class="delete-btn text-red-500 hover:underline" data-id="${rowCount + 1}">Delete</button>
        </td>
    `;

            row.querySelector('.edit-btn').addEventListener('click', function(e) {
                e.preventDefault();
                editNoteRow(row);
            });

            row.querySelector('.delete-btn').addEventListener('click', function() {
                deleteNoteRow(row);
            });

            const emptyMessage = document.getElementById('empty-message');
            if (emptyMessage) {
                emptyMessage.classList.add('hidden');
            }

            updateTotalPrice();
            console.log(noteDetails);
        }

        function deleteNoteRow(row) {
            const rowId = parseInt(row.dataset.rowId);

            // Remove from our notes array
            noteDetails = noteDetails.filter(item => item.id !== rowId);

            row.remove();
            const table = document.getElementById('note_details');

            // If all rows are deleted, show the empty message
            if (table.rows.length <= 1) { // Accounting for header row
                const tbody = table.querySelector('tbody');
                if (tbody) {
                    tbody.innerHTML = `
                <tr id="no-data-row">
                    <td class="px-4 py-3 text-center text-gray-500" colspan="5">Please insert the note detail</td>
                </tr>
            `;
                }
            }
            // Update the total price
            updateTotalPrice();
        }

        function editNoteRow(row) {
            const rowId = parseInt(row.dataset.rowId);
            const item = noteDetails.find(item => item.id === rowId);

            if (!item) return;

            Swal.fire({
                title: 'Edit Note Detail',
                html: `
        <div class="text-left">
            <div class="mb-4">
                <label for="edit-product" class="block text-gray-700 font-medium mb-2 capitalize">Product</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-md placeholder:capitalize" id="edit-product">
                    <option value="${item.productId}" data-name="${item.productName}" selected>${item.productName}</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-purchase-price="{{ $product->purchase_price }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="edit-quantity" class="block text-gray-700 font-medium mb-2 capitalize">Quantity</label>
                <input type="number" id="edit-quantity" class="border border-gray-300 rounded-md p-2 w-full" value="${item.quantity}">
            </div>

            <div class="mb-4">
                <label for="edit-price" class="block text-gray-700 font-medium mb-2 capitalize">Price</label>
                <input type="number" id="edit-price" class="border border-gray-300 rounded-md p-2 w-full" value="${item.price}">
            </div>
        </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const productSelect = document.getElementById('edit-product');
                    const productId = productSelect.value;
                    const productName = productSelect.options[productSelect.selectedIndex].dataset.name;
                    const quantity = document.getElementById('edit-quantity').value;
                    const price = document.getElementById('edit-price').value;

                    if (!productId || !quantity || !price) {
                        Swal.showValidationMessage('Please fill in all required fields');
                        return false;
                    }

                    return {
                        productId,
                        productName,
                        quantity,
                        price
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Update the note details array
                    const updatedItem = noteDetails.find(item => item.id === rowId);
                    if (updatedItem) {
                        updatedItem.productId = result.value.productId;
                        updatedItem.productName = result.value.productName;
                        updatedItem.quantity = result.value.quantity;
                        updatedItem.price = result.value.price;
                    }

                    // Update the table row
                    const cells = row.getElementsByTagName('td');
                    cells[1].textContent = result.value.productName;
                    cells[2].textContent = result.value.quantity;
                    cells[3].textContent = result.value.price;

                    // Update the total price
                    updateTotalPrice();
                    console.log(noteDetails);
                }
            });

            // Event listener for product selection in edit modal
            document.getElementById('edit-product').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const purchasePrice = selectedOption.dataset.purchasePrice;
                document.getElementById('edit-price').value = purchasePrice;
            });
        }

        // Function untuk inisialisasi tabel kosong
        function initializeEmptyTable() {
            const table = document.getElementById('note_details');
            const tbody = table.querySelector('tbody');

            if (tbody && tbody.rows.length === 0) {
                tbody.innerHTML = `
            <tr id="no-data-row">
                <td class="px-4 py-3 text-center text-gray-500" colspan="5">Please insert the note detail</td>
            </tr>
        `;

                const emptyMessage = document.getElementById('empty-message');
                if (emptyMessage) {
                    emptyMessage.classList.remove('hidden');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('#note').addEventListener('submit', function(e) {
                const noteDetailsInput = document.querySelector('#note_details_json');
                noteDetailsInput.value = JSON.stringify(noteDetails);
            });

            initializeEmptyTable();

            if (document.getElementById('total_price')) {
                updateTotalPrice();
            }
        });
    </script>
@endsection
