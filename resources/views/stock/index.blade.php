@extends('layouts.app')

@section('title', 'Stock List')

@section('content')
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 border border-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between mb-6 gap-2">
            <!-- Search -->
            <form method="GET" action="{{ route('stocks.index') }}" class="flex gap-2 flex-wrap">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search stocks..."
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">

                <div class="relative w-38">
                    <select name="sort" id="sort" onchange="this.form.submit()"
                        class="appearance-none w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg bg-white text-gray-700 font-medium text-[15px] focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                        <option value="">Sort by</option>
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

        </div>

        <!-- Responsive Table -->
        <div class="overflow-x-auto">
            <table class="w-full bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Product Name</th>
                        <th class="px-4 py-3 text-left">Purchase Price</th>
                        <th class="px-4 py-3 text-left">Sale Price</th>
                        <th class="px-4 py-3 text-center">Current Stock</th>
                        <th class="px-4 py-3 text-center">Minimum Stock</th>
                        <th class="px-4 py-3 text-center">Last Updated</th>
                        <th class="px-4 py-3 text-center">Total Sold</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stocks as $index => $stock)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-3">{{ ($stocks->currentPage() - 1) * $stocks->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-3">{{ $stock->product->name }}</td>
                            <td class="px-4 py-3">{{ number_format($stock->product->purchase_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ number_format($stock->product->sale_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">{{ $stock->currentStock }}</td>
                            <td class="px-4 py-3 text-center">{{ $stock->minimumStock }}</td>
                            <td class="px-4 py-3 text-center">
                                {{ $stock->lastUpdated ? \Carbon\Carbon::parse($stock->lastUpdated)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">{{ $stock->totalOrder }}</td>
                            <td class="px-4 py-3 text-center space-x-3">
                                <a href="{{ route('stocks.edit', $stock->id) }}"
                                    class="text-blue-500 hover:text-blue-700 transition font-medium">Edit</a>
                                <button type="button"
                                    onclick="confirmDeleteStock(event, '{{ route('stocks.destroy', $stock->id) }}')"
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
            {{ $stocks->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>

    <script>
        function confirmDeleteStock(event, deleteUrl) {
            event.preventDefault();
            Swal.fire({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this stock!",
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
