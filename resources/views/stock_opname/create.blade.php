@extends('layouts.app')

@section('title', 'New Stock Opname')

@section('content')
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-lg mt-6">

        <form action="{{ route('opname.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="product_id" class="block text-sm font-bold mb-2">Product</label>
                <select id="product_id" name="product_id"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="system_stock" class="block font-bold mb-2">System Stock</label>
                <input type="text" name="system_stock" id="system_stock" readonly
                    class="w-full px-4 py-2 border border-gray-600 rounded-md bg-gray-100">
            </div>

            <div class="mb-4">
                <label for="actual_stock" class="block font-bold mb-2">Actual Stock</label>
                <input type="number" name="actual_stock" id="actual_stock" class="w-full border rounded px-3 py-2"
                    required>
            </div>

            <div class="mb-4">
                <label for="difference" class="block font-bold mb-2">difference</label>
                <input type="text" name="difference" id="difference" readonly
                    class="w-full px-4 py-2 border border-gray-600 rounded-md bg-gray-100">
            </div>

            <div class="mb-4">
                <label for="note" class="block font-bold mb-2">Note (optional)</label>
                <textarea name="note" id="note" class="w-full border rounded px-3 py-2"></textarea>
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('opname.index') }}"
                    class="bg-red-500 text-white px-5 py-2 rounded-lg hover:bg-red-600">Cancel</a>
                <button type="submit"
                    class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700">Save</button>
            </div>
        </form>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle product selection
            $('#product_id').change(function() {
                const productId = $(this).val(); // Get the selected product ID
    
                if (productId) {
                    // Send an AJAX request to fetch system stock for the selected product
                    $.ajax({
                        url: '/getSystemStock',
                        type: 'GET',
                        data: { product_id: productId },
                        success: function(response) {
                            if (response.system_stock) {
                                // Set the system stock value
                                $('#system_stock').val(response.system_stock.toLocaleString());
                                updateDifference(); // Recalculate the difference after setting the system stock
                            } else {
                                $('#system_stock').val('0');
                                updateDifference(); // Recalculate the difference if no stock is found
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching stock:', error);
                        }
                    });
                } else {
                    // If no product is selected, clear the system stock and difference
                    $('#system_stock').val('');
                    $('#difference').val('');
                }
            });
    
            // Handle actual stock input change
            $('#actual_stock').on('input', function() {
                updateDifference(); // Recalculate the difference whenever actual stock is updated
            });
    
            // Function to update the difference field
            function updateDifference() {
                const systemStock = parseFloat($('#system_stock').val().replace(/,/g, '')) || 0; // Get system stock
                const actualStock = parseFloat($('#actual_stock').val()) || 0; // Get actual stock
                const difference = systemStock - actualStock; // Calculate the difference
    
                // Update the difference field
                $('#difference').val(difference.toLocaleString());
            }
        });
    </script>
@endsection


