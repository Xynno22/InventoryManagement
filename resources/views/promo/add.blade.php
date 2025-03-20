@extends('layouts.app')
@section('title', 'Add Promo')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Add Promo</h2>

        <form action="{{ route('promo.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Promo Name</label>
                <input
                    type="text"
                    name="name"
                    placeholder='Promo Name'
                    class="w-full px-4 py-2 border border-gray-600 rounded-md"
                    value="{{old('name')}}"
                    required
                >
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


            <div class="mb-6">
                <label for="end_date" class="block text-gray-800 font-semibold mb-2">
                    End Date
                </label>
                <input
                        type="datetime-local"
                        name="end_date"
                        id="end_date"
                        placeholder="Select end date & time"
                        class="w-full px-4 py-2 border border-gray-600 rounded-lg shadow-sm"
                        onfocus="this.showPicker()"
                        value="{{old('end_date')}}"
                    required
                />
                @error('end_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="type" class="block text-gray-700 font-medium mb-2">Promo Type</label>
                <select class="w-full p-2 border border-black rounded-md" name="type" id="type">
                    <option disabled {{ old('type') ? '' : 'selected' }}>Select the Promo Type</option>
                    @foreach ($promoTypes as $promoType)
                        <option value="{{ $promoType->name }}" class="capitalize"
                                                               {{ strtolower(old('type')) == strtolower($promoType->name) ? 'selected' : '' }}>
                {{ $promoType->name }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

            </div>


            {{-- Amount for percentage Type --}}
            <div class="mb-4 {{ old('type') && str_contains(old('type'), 'percentage') ? '' : 'hidden' }}" id="inputPercentage">
                <label for="amount" class="block text-gray-700 font-medium mb-2">Amount</label>
                <div class="flex items-center justify-start">
                    <div>
                        <input
                            type="text"
                            name="percentageamount"
                            id="percentageamount"
                            placeholder="Percentage Discount"
                            class="w-20 px-4 text-center py-2 border border-gray-300"
                            value="{{ old('percentageamount')}}"
                            >
                    </div>
                    <h1 class="bg-gray-50 px-4 py-2">%</h1>
                </div>
                @error('amount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                @error('percentageamount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

            </div>

            {{-- Amount for Fixed Amount Type --}}
            <div class="mb-4 {{ old('type') && str_contains(old('type'), 'amount') ? '' : 'hidden' }}" id="inputAmount">
                <label for="amount" class="block text-gray-700 font-medium ">Amount</label>
                <small class="italic text-gray-400 ">
                    diisi dengan <b>TIDAK</b> menggunakan titik. Cth: 50000
                </small>
                <div class="flex items-center mt-2">
                    <h1 class="bg-gray-50 px-4 py-2">Rp.</h1>
                    <div class="w-full">
                        <input type="text"
                            name="fixedamount"
                            id="fixedamount"
                            placeholder="Input the amount"
                            class="w-full px-4 py-2 border border-gray-300"
                            value="{{ old('fixedamount')}}"
                            >
                    </div>
                </div>
                @error('amount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                @error('fixedamount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('promo.index') }}"
                   class="bg-red-500 text-white px-5 py-2 rounded-lg hover:bg-red-600 transition">
                    Cancel
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Save
                </button>
            </div>
        </form>
    </div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const promoTypeSelect = document.getElementById("type");
        const percentageInput = document.getElementById("percentageamount");
        const fixedAmountInput = document.getElementById("fixedamount");
        const inputPercentageDiv = document.getElementById("inputPercentage");
        const inputAmountDiv = document.getElementById("inputAmount");

        function toggleInputFields() {
            const selectedType = promoTypeSelect.value.toLowerCase();

            if (selectedType.includes("percentage")) {
                inputPercentageDiv.classList.remove("hidden");
                inputAmountDiv.classList.add("hidden");
                fixedAmountInput.value = ""; // Hapus nilai input fixed amount
            } else if (selectedType.includes("amount")) {
                inputPercentageDiv.classList.add("hidden");
                inputAmountDiv.classList.remove("hidden");
                percentageInput.value = ""; // Hapus nilai input percentage
            } else {
                inputPercentageDiv.classList.add("hidden");
                inputAmountDiv.classList.add("hidden");
                percentageInput.value = "";
                fixedAmountInput.value = "";
            }
        }

        promoTypeSelect.addEventListener("change", toggleInputFields);
        toggleInputFields(); // Panggil saat halaman dimuat untuk mengatur default
    });
</script>
@endsection
