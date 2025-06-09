@extends('layouts.app')

@section('title', 'Edit Operational Expenses')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">


        @if ($errors->any())
            <div class="bg-red-500 text-white p-3 rounded-md mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('operational.update', $operational->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="date" class="block text-gray-700 font-medium mb-2">Date</label>
                <input type="date" name="date" id="date" value="{{ old('date', $operational->date) }}"
                    class="bg-white w-full px-4 py-2 border border-gray-600 rounded-md">
            </div>

            <div class="mb-4">
                <label for="amount" class="block text-gray-700 font-medium mb-2">Amount</label>
                <input type="number" name="amount" id="amount" value="{{ old('amount', $operational->amount) }}"
                    class="w-full px-4 py-2 border border-gray-600 rounded-md">
            </div>
            <div class="mb-4">
                <label for="payment_id" class="block text-gray-700 font-medium mb-2">Payment Method</label>
                <select name="payment_id" id="payment_id"
                    class="bg-white w-full px-4 py-2 border border-gray-600 rounded-md">
                    <option value="">-- Select Payment Method --</option>
                    @foreach ($payments as $payment)
                        <option value="{{ $payment->id }}"
                            {{ old('payment_id', $operational->payment_id) == $payment->id ? 'selected' : '' }}>
                            {{ $payment->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="note" class="block text-gray-700 font-medium mb-2">Note</label>
                <textarea name="note" id="note" rows="4" class="w-full px-4 py-2 border border-gray-600 rounded-md">{{ old('note', $operational->note) }}</textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <a href="{{ route('operational.index') }}"
                    class="bg-red-500 text-white px-5 py-2 rounded-lg hover:bg-red-600 transition">
                    Cancel
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Update
                </button>
            </div>
        </form>
    </div>
@endsection
