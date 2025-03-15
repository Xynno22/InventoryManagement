@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Add Promo</h2>

        @if ($errors->any())
            <div class="bg-red-500 text-white p-3 rounded-md mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('promo.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Promo Name</label>
                <input type="text" name="name" placeholder='Promo Name'
                                   class="w-full px-4 py-2 border border-gray-600 rounded-md ">
            </div>

            <div class="mb-4">
                <label for="type" class="block text-gray-700 font-medium mb-2">Promo Type</label>
                <select class="w-full p-2 border border-black rounded-md" name="type" id="type">
                    <option disabled selected>Select the Promo Type</option>
                    @foreach ($promoTypes as $promoType)
                        <option value="{{ strtolower($promoType->name) }}" class="capitalize">{{ $promoType->name }}</option>
                    @endforeach
                </select>
            </div>


            {{-- Amount for percentage Type --}}
            <div class="mb-4 hidden" id="inputPercentage">
                <label for="amount" class="block text-gray-700 font-medium mb-2">Amount</label>
                <div class="flex items-center justify-start">
                    <div>
                        <input
                            type="text"
                            name="percentageamount"
                            id="percentageamount"
                            placeholder="Percentage Discount"
                            class="w-20 px-4 text-center py-2 border border-gray-300">
                    </div>
                    <h1 class="bg-gray-50 px-4 py-2">%</h1>
                </div>
            </div>

            {{-- Amount for Fixed Amount Type --}}
            <div class="mb-4 hidden" id="inputAmount">
                <label for="amount" class="block text-gray-700 font-medium ">Amount</label>
                <small class="italic text-red-500 ">
                    diisi dengan tidak menggunakan titik. Cth: 50000
                </small>
                <div class="flex items-center mt-2">
                    <h1 class="bg-gray-50 px-4 py-2">Rp.</h1>
                    <div class="w-full">
                        <input type="text"
                            name="fixedamount"
                            id="fixedamount"
                            placeholder="Input the amount"
                            class="w-full px-4 py-2 border border-gray-300">
                    </div>
                </div>
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
document.getElementById("type").addEventListener("change", function () {
    const inputAmount = document.getElementById("inputAmount");
    const inputPercentage = document.getElementById("inputPercentage");

    const selectedType = this.value.toLowerCase(); // Pastikan lowercase untuk perbandingan yang aman

    if (selectedType.includes("percentage")) {
        inputPercentage.classList.remove("hidden");
        inputAmount.classList.add("hidden");
    } else if (selectedType.includes("amount")) {
        inputAmount.classList.remove("hidden");
        inputPercentage.classList.add("hidden");
    } else {
        inputAmount.classList.add("hidden");
        inputPercentage.classList.add("hidden");
    }
});
    </script>
@endsection
