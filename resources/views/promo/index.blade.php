@extends('layouts.app')

@section('title', 'Promo & Discount')

@section('content')
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">
        <div class="flex justify-end mb-6">
            <a href="{{ route('promo.create')}}"
                class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                Add Promo
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Promo Name</th>
                        <th class="px-4 py-3 text-left">Promo Type</th>
                        <th class="px-4 py-3 text-center">Amount</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($promos as $promo)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-3">{{ ($promos->currentPage() - 1) * $promos->perPage() + $loop->iteration }}</td>
                            <td class="px-4 py-2">{{ $promo->name }}</td>
                            <td class="px-4 py-2 capitalize">{{ $promo->promoType->name }}</td>
                            <td class="px-4 py-2 text-center font-bold">
                                @if ($promo->promoType->name == 'percentage')
                                    {{ intval($promo->amount) }}%
                                @else
                                    Rp{{ number_format($promo->amount, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center space-x-3">
                                <a href="{{ route('promo.edit', $promo->id) }}" class="text-blue-500 hover:text-blue-700 transition font-medium">Edit</a>
                                <button type="button"
                                        onclick="confirmDeletePromo(event, '{{ route('promo.destroy', $promo->id) }}')"
                                        class="text-red-500 hover:text-red-700 transition font-medium">
                                        Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $promos->links('pagination::tailwind') }}
        </div>
    </div>

    <script>
        function confirmDeletePromo(event, deleteUrl) {
            event.preventDefault();
            Swal.fire({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this category!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, Delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(deleteUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'DELETE'
                        })
                    }).then(() => location.reload());
                }
            });
        }
    </script>
@endsection
