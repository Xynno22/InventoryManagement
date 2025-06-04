@extends('layouts.app')

@section('title', 'Note')

@if (session('success'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                toast: true,
                position: "bottom-end",
                icon: "success",
                title: "Note {{ session('success') }} successfully",
                showConfirmButton: false,
                width: "auto",
                timer: 2000,
                timerProgressBar: true,
                html: `<p class="text-[14px] font-light">Your note successfully {{ session('success') }}!</p>`,
                customClass: {
                    popup: 'py-3 px-4 rounded-[10px] ring-2 ring-[#82e095]/80 font-[inter]',
                    icon: "!mr-4",
                    title: "!p-0 !m-0 !mb-[2px] text-md font-semibold m-0 text-black",
                    htmlContainer: '!p-0 !m-0',
                    timerProgressBar: 'bg-[#f1f9f4]',
                }
            });
        });
    </script>
@endif

@section('content')
    {{-- @if (session('success')) --}}
    {{--       <div class="bg-green-500 text-white p-3 rounded-md mb-4"> --}}
    {{--           {{ session('success') }} --}}
    {{--       </div> --}}
    {{--   @endif --}}
    {{-- --}}
    {{--   @if (session('error')) --}}
    {{--       <div class="bg-red-500 text-white p-3 rounded-md mb-4"> --}}
    {{--           {{ session('error') }} --}}
    {{--       </div> --}}
    {{--   @endif --}}

    <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">

        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
            <!-- Search and Sorting -->
            <div class="flex items-center gap-4">
                <form method="GET" action="{{ route('note.index') }}" class="flex flex-wrap gap-2 w-full md:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Note..."
                    class="px-4 py-2 border border-gray-300 rounded-lg w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </form>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Search
                </button>
            </div>
            <div class="flex justify-end md:justify-start gap-3 w-full md:w-auto">
                @if (Auth::guard('company')->check() || Auth::user()->can('create operational Note'))
                    <a href="{{ route('note.create') }}"
                        class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2 w-full md:w-auto text-center justify-center">
                        Add Note
                    </a>
                @endif
            </div>
        </div>


        <div class="overflow-x-auto">
            <table class="w-full bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
                <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="px-4 py-3 text-center">No</th>
                        {{-- <th class="px-4 py-3 text-center">Voucher No</th> --}}
                        <th class="px-4 py-3 text-center">Customer Name</th>
                        <th class="px-4 py-3 text-center">Description</th>
                        <th class="px-4 py-3 text-center">Total Price</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>

                <tbody class="text-center">
                    @foreach ($notes as $index => $note)
                        <tr class="border-gray-200 border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-3">{{ $index + $notes->firstItem() }}</td>
                            {{-- <td class="px-4 py-3">{{ $note->voucher_code }}</td> --}}
                            <td class="px-4 py-3 font-semibold">{{ $note->customer_name }}</td>
                            <td class="px-4 py-3 w-64 truncate overflow-hidden whitespace-nowrap max-w-xs">
                                {{ $note->description }}</td>



                            {{-- <td class="px-4 py-3 text-center"> --}}
                            {{--     {{ \Carbon\Carbon::parse($note->date)->format('d-M-Y') }} --}}
                            {{-- </td> --}}
                            <td>Rp {{ number_format($note->total_price, 0, ',', '.') }}</td>
                            <!-- Menampilkan grand_total -->

                            <td class="px-4 py-3 text-center space-x-3">
                                <div class="flex gap-3 items-center justify-center">
                                    @if (Auth::guard('company')->check() == true || Auth::user()->can('view operational Note'))
                                        <a href="{{ route('note.show', $note) }}">
                                            <div class="relative group">
                                                <div class="p-[5px] bg-green-50 rounded-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-5 text-green-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    </svg>
                                                </div>
                                                <span
                                                    class="capitalize absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md px-2 py-1">
                                                    View note Detail
                                                </span>
                                            </div>
                                        </a>
                                    @endif

                                    @if (Auth::guard('company')->check() == true || Auth::user()->can('update operational Note'))
                                        <a href="{{ route('note.edit', $note) }}">
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
                                                    class="capitalize absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md px-2 py-1">
                                                    Edit note
                                                </span>
                                            </div>
                                        </a>
                                    @endif


                                    @if (Auth::guard('company')->check() == true || Auth::user()->can('view operational Note'))
                                        <a href="{{ route('note.compare', $note->id) }}">
                                            <div class="relative group">
                                                <div class="p-[5px] bg-cyan-50 rounded-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-5 text-cyan-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971Z" />
                                                    </svg>
                                                </div>
                                                <span
                                                    class="capitalize absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md px-2 py-1">
                                                    Compare Note
                                                </span>
                                            </div>
                                        </a>
                                    @endif

                                    @if (Auth::guard('company')->check() == true || Auth::user()->can('delete operational Note'))
                                        <div class="relative group">
                                            <button
                                                onclick="confirmDeletePromo(event, '{{ route('note.destroy', $note->id) }}')"
                                                class="p-[5px] bg-red-50 rounded-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-red-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                            <span
                                                class="capitalize absolute left-1/2 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md px-2 py-1">
                                                Delete note
                                            </span>
                                        </div>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $notes->links('pagination::tailwind') }}
        </div>
    </div>

    <script>
        function confirmDeletePromo(event, deleteUrl) {
            event.preventDefault();
            Swal.fire({
                title: `<h1 class="font-bold">Are you sure want to<br/> delete this note?</h1>`,
                showCancelButton: true,
                imageUrl: "https://cdn-icons-png.flaticon.com/512/3300/3300464.png",
                imageWidth: 100,
                imageHeight: 100,
                html: `<p class="text-[.98rem]">You won't be able to revert this!</p>`,
                imageAlt: "Delete Icon",
                confirmButtonColor: "#d33",
                reverseButtons: 'true',
                customClass: {
                    popup: "rounded-2xl max-w-md",
                    cancelButton: "bg-white text-gray-700 hover:text-white hover:bg-gray-400 transition-all duration-150 ring-2 ring-[#eaeaea] py-[4px] px-12",
                    confirmButton: "py-[6px] px-12 rounded-md",
                    title: "p-0",
                },
                confirmButtonText: "Delete"
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
                    position: "bottom-end",
                    icon: "success",
                    title: "Note deleted Successfully!",
                    html: `<p class="text-[14px] font-light">Your note has been deleted</p>`,
                    showConfirmButton: false,
                    width: "400px", // Atur lebar toast
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'py-3 px-4 rounded-[10px] ring-2 ring-[#82e095]/80 font-[inter]',
                        icon: "!mr-4", // Tambahkan margin ke ikon
                        title: "!p-0 !m-0 !mb-[2px] text-md font-semibold m-0 text-black",
                        htmlContainer: '!p-0 !m-0',
                        timerProgressBar: 'bg-[#f1f9f4]',
                    }

                });
            }
        });
    </script>
@endsection
