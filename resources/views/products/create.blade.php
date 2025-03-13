@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Product Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    placeholder="Enter product name"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category -->
            <div class="mb-4">
                <label for="categoryID" class="block text-sm font-medium text-gray-700">Category</label>
                <select id="categoryID" name="categoryID"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('categoryID') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('categoryID')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buying Price -->
            <div class="mb-4">
                <label for="purchase_price" class="block text-sm font-medium text-gray-700">Purchase Price</label>
                <input type="number" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}"
                    step="0.01" placeholder="Enter buying price"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                @error('purchase_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Selling Price -->
            <div class="mb-4">
                <label for="sale_price" class="block text-sm font-medium text-gray-700">Sale Price</label>
                <input type="number" id="sale_price" name="sale_price" value="{{ old('sale_price') }}" step="0.01"
                    placeholder="Enter selling price"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                @error('sale_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Unit -->
            <div class="mb-4">
                <label for="unit" class="block text-sm font-medium text-gray-700">Unit</label>
                <input type="text" id="unit" name="unit" value="{{ old('unit') }}"
                    placeholder="e.g., kg, pcs, liter"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                @error('unit')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Product Location -->
            <div class="mb-4">
                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                <input type="text" id="location" name="location" value="{{ old('location') }}"
                    placeholder="Enter product location"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                @error('location')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Product Details -->
            <div class="mb-4">
                <label for="detail" class="block text-sm font-medium text-gray-700">Product Details</label>
                <textarea id="detail" name="detail" rows="4" placeholder="Enter product details or description"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('details') }}</textarea>
                @error('detail')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image Upload -->
            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                
                <div id="dropArea" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-indigo-500 transition">
                    <input type="file" id="image" name="image" accept="image/*" class="hidden" onchange="previewImage(event)">
                    <img id="preview" class="mx-auto hidden rounded-lg" style="max-height: 150px;" />
                    
                    <div id="uploadText">
                        <svg class="mx-auto w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V12M17 16V12M12 8V4M4 12h16M4 16h16"></path>
                        </svg>
                        <p class="text-indigo-600 font-semibold mt-2">Upload a file</p>
                        <p class="text-gray-500 text-sm">or drag and drop</p>
                        <p class="text-gray-400 text-xs">PNG, JPG, GIF up to 10MB</p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('products.index') }}"
                    class="bg-gray-500 text-white px-5 py-2 rounded-lg hover:bg-gray-600 transition">Cancel</a>
                <button type="submit"
                    class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">Save</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('dropArea').addEventListener('click', function() {
            document.getElementById('image').click();
        });
    
        function previewImage(event) {
            let reader = new FileReader();
            reader.onload = function() {
                let preview = document.getElementById('preview');
                let uploadText = document.getElementById('uploadText');
                preview.src = reader.result;
                preview.classList.remove('hidden');
                uploadText.classList.add('hidden');
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
