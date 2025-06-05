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
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition w-full sm:w-auto">
                    Search
                </button>
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
            @if ($operationals->count() > 0)
            <table class="w-full bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Amount</th>
                        <th class="px-4 py-3 text-left">Note</th>
                        <th class="px-4 py-3 text-left">Payment</th>
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
                            <td class="px-4 py-3">{{ $item->payment->name }}</td>
                            <td class="px-4 py-3 text-center space-x-3">
                                <div class="flex gap-3 items-center justify-center">
                                @if (Auth::guard('company')->check() == true || Auth::user()->can('delete operational expenses'))
                                    <a href="{{ route('operational.edit', $item->id) }}"
                                        class="text-blue-500 hover:text-blue-700 transition font-medium">

                                            <div class="relative group">
                                                <div class="p-[5px] bg-blue-50 rounded-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-5 text-blue-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                    </svg>
                                                </div>
                                                <span
                                                    class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md px-2 py-1">
                                                    Edit Operational Expense
                                                </span>
                                            </div>
                                    </a>
                                @endif

                                @if (Auth::guard('company')->check() == true || Auth::user()->can('delete operational expenses'))
                                    <div class="relative group">
                                        <button type="button"
                                                onclick="confirmDeleteOperational(event, '{{ route('operational.destroy', $item->id) }}')"
                                                class="p-[5px] bg-red-50 rounded-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-red-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                        </button>
                                        <span
                                            class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md px-2 py-1">
                                            Delete Operational Expense
                                        </span>
                                    </div>
                                @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <!-- No Data Found Message -->
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    @if (request('search'))
                        No Operational Expense found for "{{ request('search') }}"
                    @else
                        No Operational Expense available
                    @endif
                </h3>
                <p class="text-gray-500 mb-6">
                    @if (request('search'))
                        Try adjusting your search criteria or browse all operationals.
                    @else
                        Get started by creating your first operational.
                    @endif
                </p>
                @if (request('search'))
                    <a href="{{ route('categories.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition mr-3">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                        Clear Search
                    </a>
                @endif
            </div>
            @endif
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
