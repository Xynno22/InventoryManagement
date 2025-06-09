@extends('layouts.app')

@section('title', 'About Product')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">
        <!-- Product Image -->
        <div class="flex flex-col md:flex-row gap-6">
            <div class="w-full md:w-1/2 flex justify-center">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                    >
            </div>

            <!-- Product Info -->
            <div class="w-full md:w-1/2">
                <h1 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h1>
                <p class="text-gray-500 text-sm">{{ $product->product_category->name ?? 'No Category' }}</p>

                <div class="mt-4">
                    <p><strong class="text-gray-700">Unit:</strong> {{ $product->unit }}</p>
                    <p><strong class="text-gray-700">Location:</strong> {{ $product->location }}</p>
                    <p class="text-gray-700"><strong>Purchase Price:</strong> IDR
                        {{ number_format($product->purchase_price, 0, ',', '.') }}</p>
                    <p class="text-gray-700"><strong>Sale Price:</strong> IDR
                        {{ number_format($product->sale_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="mt-6 p-4 bg-gray-100 rounded-lg">
            <h2 class="text-lg font-bold text-gray-700 mb-3">Product Details:</h2>
            <div class="text-gray-600 text-sm space-y-1">
                {!! nl2br(e($product->detail)) !!}
            </div>
        </div>

        <!-- Back Button -->
        <div class="text-right mt-4">
            <a href="{{ route('products.index') }}"
                class="bg-blue-500 text-white text-xs font-semibold px-3 py-2 rounded-md hover:bg-blue-600 transition">
                ← Back to Products
            </a>
        </div>
    </div>
@endsection
