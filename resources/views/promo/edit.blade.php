@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Edit Promo</h2>

        <div class="mb-4 p-4 bg-gray-100 rounded-md">
            <h3 class="text-lg font-semibold">Promo Details</h3>
            <ul class="list-disc list-inside text-gray-700">
                                {{ $promo->promoType->name}}
                                {{ $promo->amount}}
                {{-- @foreach($promo->getAttributes() as $key => $value) --}}
                {{--     <li> --}}
                {{--         <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> --}}
                {{--         @if($key === 'promo_type_id') --}}
                {{--             {{ $promo->promoType ? $promo->promoType->name : 'N/A' }} --}}
                {{--         @else --}}
                {{--             {{ $value }} --}}
                {{--         @endif --}}
                {{--     </li> --}}
                {{-- @endforeach --}}
            </ul>
        </div>
        @if ($errors->any())
            <div class="bg-red-500 text-white p-3 rounded-md mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('promo.update', $promo->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Promo Name</label>
                <input type="text"
                       name="name" placeholder='Promo Name'
                                   value="{{ old('name', $promo->name) }}"
                                   class="w-full px-4 py-2 border border-gray-600 rounded-md">
            </div>

            <div class="mb-4">
                <label for="type" class="block text-gray-700 font-medium mb-2">Promo Type</label>
                <select class="w-full p-2 border border-black rounded-md" name="type" id="type">
                    <option disabled>Select the Promo Type</option>
                    @foreach ($promoTypes as $promoType)
                        <option value="{{ strtolower($promoType->name) }}"
                                class="capitalize"
                                {{ strtolower($promo->promoType->name ?? '') == strtolower($promoType->name) ? 'selected' : '' }}>
                {{ $promoType->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Amount for Percentage Type --}}
            <div class="mb-4 {{ strtolower($promo->promoType->name) == 'percentage' ? '' : 'hidden' }}" id="inputPercentage">
                <label for="percentageamount" class="block text-gray-700 font-medium mb-2">Percentage Amount</label>
                <div class="flex items-center">
                    <input type="text" name="percentageamount"
                                       id="percentageamount"
                                       placeholder="Percentage Discount"
                                       value="{{ old('percentageamount', strtolower($promo->promoType->name) == 'percentage' ? intval($promo->amount) : '') }}"
                                       class="w-20 px-4 text-center py-2 border border-gray-300">
                                       <h1 class="bg-gray-50 px-4 py-2">%</h1>
                </div>
            </div>

            {{-- Amount for Fixed Amount Type --}}
            <div class="mb-4 {{ strtolower($promo->promoType->name) == 'fixed amount' ? '' : 'hidden' }}" id="inputAmount">
                <label for="fixedamount" class="block text-gray-700 font-medium">Fixed Amount</label>
                <small class="italic text-red-500">Diisi tanpa titik. Cth: 50000</small>
                <div class="flex items-center mt-2">
                    <h1 class="bg-gray-50 px-4 py-2">Rp.</h1>
                    <div class="w-full">
                        <input type="text" name="fixedamount" id="fixedamount"
                                                              placeholder="Input the amount"
                                                              value="{{ old('fixedamount', strtolower($promo->promoType->name) == 'fixed amount' ? intval($promo->amount) : '') }}"
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
                    Update
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById("type").addEventListener("change", function () {
            const inputAmount = document.getElementById("inputAmount");
            const inputPercentage = document.getElementById("inputPercentage");

            const selectedType = this.value.toLowerCase();

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

        // Jalankan saat halaman pertama kali dimuat
        window.addEventListener("DOMContentLoaded", function () {
            document.getElementById("type").dispatchEvent(new Event("change"));
        });

    </script>
@endsection
