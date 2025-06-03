@extends('layouts.app')

@section('title', 'Stock Opname')

@section('content')
    <div class="max-w-5xl mx-auto p-6 bg-white rounded-lg shadow-lg mt-6">
        <div class="flex justify-between mb-4">
            <h1 class="text-2xl font-bold">Stock Opname</h1>
            @if (Auth::guard('company')->check() == true || Auth::user()->can('create stock opname'))
                <a href="{{ route('opname.create') }}"
                    class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700">
                    New Opname
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Product</th>
                        <th class="px-4 py-2">System Stock</th>
                        <th class="px-4 py-2">Actual Stock</th>
                        <th class="px-4 py-2">Difference</th>
                        <th class="px-4 py-2">Note</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stockOpnames as $opname)
                        <tr class="border-b text-center">
                            <td class="px-4 py-2">{{ $opname->product->name }}</td>
                            <td class="px-4 py-2">{{ $opname->system_stock }}</td>
                            <td class="px-4 py-2">{{ $opname->actual_stock }}</td>
                            <td class="px-4 py-2">{{ $opname->difference }}</td>
                            <td class="px-4 py-2">{{ $opname->note }}</td>
                            <td class="px-4 py-3 space-x-3">
                                @if (Auth::guard('company')->check() == true || Auth::user()->can('delete stock opname'))
                                    <button type="button"
                                        onclick="confirmDeleteOpname(event, '{{ route('opname.destroy', $opname->id) }}')"
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

        <div class="mt-4">
            {{ $stockOpnames->links() }}
        </div>
    </div>
    <script>
        function confirmDeleteOpname(event, deleteUrl) {
            event.preventDefault();
            Swal.fire({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this Stock Opname!",
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
