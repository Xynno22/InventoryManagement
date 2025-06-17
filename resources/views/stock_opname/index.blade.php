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
                                    <div class="relative group">
                                        <button type="button"
                                                onclick="confirmDeleteOpname(event, '{{ route('opname.destroy', $opname->id) }}')"
                                                class="p-[5px] bg-red-50 rounded-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-red-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                        </button>
                                        <span
                                            class="max-w-[85px] absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md px-2 py-1 z-[99999] w-max  text-center leading-tight">
                                            Delete Stock Opname
                                        </span>
                                    </div>
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
