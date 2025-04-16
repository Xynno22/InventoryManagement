@extends('layouts.app')

@section('title', 'Operational Expenses')

@section('content')
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 border border-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between mb-6 gap-2">
            <!-- Search and Sorting -->
            <form method="GET" action="{{ route('operational.index') }}" class="flex gap-2 flex-wrap">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search operational..."
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">

                <div class="relative w-38">
                    <select name="sort" id="sort" onchange="this.form.submit()"
                        class="appearance-none w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg bg-white text-gray-700 font-medium text-[15px] focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm">
                        <option value="">Sort by Date</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Newest First</option>
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

            <!-- Add Operational -->
            @if (Auth::guard('company')->check() == true || Auth::user()->can('delete operational expenses'))
                <a href="{{ route('operational.create') }}"
                    class="bg-indigo-600 text-white px-5 py-2 h-[40px] flex items-center rounded-lg hover:bg-indigo-700 transition gap-2">
                    Add
                </a>
            @endif

        </div>

        <div class="overflow-x-auto">
            <table class="w-full bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Amount</th>
                        <th class="px-4 py-3 text-left">Note</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($operationals as $item)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                {{ ($operationals->currentPage() - 1) * $operationals->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $item->note }}</td>
                            <td class="px-4 py-3 text-center space-x-3">
                                @if (Auth::guard('company')->check() == true || Auth::user()->can('delete operational expenses'))
                                    <a href="{{ route('operational.edit', $item->id) }}"
                                        class="text-blue-500 hover:text-blue-700 transition font-medium">Edit</a>
                                @endif

                                @if (Auth::guard('company')->check() == true || Auth::user()->can('delete operational expenses'))
                                    <button type="button"
                                        onclick="confirmDeleteOperational(event, '{{ route('operational.destroy', $item->id) }}')"
                                        class="text-red-500 hover:text-red-700 transition font-medium">
                                        Delete
                                    </button>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $operationals->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>

    <script>
        function confirmDeleteOperational(event, deleteUrl) {
            event.preventDefault();
            Swal.fire({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this expense!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
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
