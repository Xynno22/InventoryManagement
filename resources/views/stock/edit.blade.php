@extends('layouts.app')
@section('title', 'Update Minimum Stock')
@section('content')
<div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-xl font-bold mb-4">Edit Minimum Stock</h2>

    @if(session('error'))
        <div class="bg-red-200 text-red-700 p-2 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('stocks.update', $stock->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">Product Name</label>
            <input type="text" class="w-full border rounded p-2 bg-gray-200" value="{{ $stock->product->name }}" readonly>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">Current Minimum Stock</label>
            <input type="number" name="minimumStock" class="w-full border rounded p-2" value="{{ old('minimumStock', $stock->minimumStock) }}" required>
            @error('minimumStock')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end">
            <a href="{{ route('stocks.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded mr-2">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
        </div>
    </form>
</div>
@endsection
