@extends('layouts.app')

@section('title', 'Promo & Discount')

@if(session('success'))
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        });
    </script>
@endif

@section('content')
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">

        <div class="flex justify-between mb-6 items-center">

            <!-- Search and Sorting -->
            <form method="GET" action="{{ route('promo.index') }}" class="flex gap-2 flex-wrap">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search promo..."
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">

                <div class="relative w-38">
                    <select name="sort" id="sort" onchange="this.form.submit()"
                        class="appearance-none w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg bg-white text-gray-700 font-medium text-[15px] focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                        <option value="" disabled>Sort by</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>A - Z</option>
                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Z - A</option>
                    </select>
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

            </form>

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
                        <th class="px-4 py-3 text-center">Promo End Date</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($promos->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center text-red-500 uppercase italic font-bold py-4 text-gray-500">
                                No promos available.
                            </td>
                        </tr>
                    @else
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
                                <td class="px-4 py-2 capitalize text-center">
                                    {{ \Carbon\Carbon::parse($promo->end_date)->translatedFormat('d F Y | H:i') }}
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
                    @endif</tbody>
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
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                sessionStorage.setItem("deleted", "true");
                                location.reload();
                            } else {
                                throw new Error("Failed to delete promo");
                            }
                        })
                        .catch(() => {
                            Swal.fire({
                                toast: true,
                                position: "top-end",
                                icon: "error",
                                title: "Failed to delete promo!",
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });
                        });
                }
            });
        }

        // Cek setelah reload apakah ada status "deleted"
        window.addEventListener("DOMContentLoaded", () => {
            if (sessionStorage.getItem("deleted") === "true") {
                sessionStorage.removeItem("deleted"); // Hapus status setelah ditampilkan
                Swal.fire({
                    toast: true,
                    position: "top-end",
                    icon: "success",
                    title: "Promo deleted successfully!",
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            }
        });
    </script>
@endsection
