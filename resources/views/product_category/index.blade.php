@extends('layouts.app')

@section('title', 'Product Categories')

@section('content')
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 border border-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        <div class="flex justify-between mb-6 gap-2">
            <!-- Search and Sorting -->
            <form method="GET" action="{{ route('categories.index') }}" class="flex gap-2 flex-wrap">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..."
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

            <!-- Add Category -->
            @if (Auth::guard('company')->check() == true || Auth::user()->can('create category'))
                <a href="{{ route('categories.create') }}"
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
                        <th class="px-4 py-3 text-left">Category Name</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
                            <td class="px-4 py-3">{{ $category->name }}</td>
                            <td class="px-4 py-3 text-center space-x-3">
                                @if (Auth::guard('company')->check() == true || Auth::user()->can('update category'))
                                    <a href="{{ route('categories.edit', $category->id) }}"
                                        class="text-blue-500 hover:text-blue-700 transition font-medium">Edit</a>
                                @endif
                                @if (Auth::guard('company')->check() == true || Auth::user()->can('delete category'))
                                    <button type="button"
                                        onclick="confirmDeleteCategory(event, '{{ route('categories.destroy', $category->id) }}')"
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
            {{ $categories->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>

    <script>
        function confirmDeleteCategory(event, deleteUrl) {
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
