@extends('layouts.app')

@section('title', 'Manage Roles')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-4">Manage Roles</h2>

    <!-- Form Tambah Role -->
    <form action="{{ route('roles.store') }}" method="POST" class="mb-6">
        @csrf
        <div class="flex items-center space-x-4">
            <input type="text" name="name" placeholder="Role Name" required
                class="border border-gray-300 p-2 rounded w-full">
            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Add Role
            </button>
        </div>
        @error('name')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </form>

    <!-- Daftar Role -->
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="border border-gray-300 px-4 py-2">#</th>
                <th class="border border-gray-300 px-4 py-2">Role Name</th>
                <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $index => $role)
            <tr>
                <td class="border border-gray-300 px-4 py-2">{{ $index + 1 }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $role->name }}</td>
                <td class="border border-gray-300 px-4 py-2 flex gap-1">
                    <a href="{{ route('roles.permissions.edit', $role->id) }}"
                        class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition">
                        Manage
                    </a>
                    <form id="delete-form-{{ $role->id }}" action="{{ route('roles.destroy', $role->id) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDeleteRole({{ $role->id }})"
                            class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- JavaScript untuk Konfirmasi Hapus -->
<script>
    function confirmDeleteRole(roleId) {
        Swal.fire({
            title: "Are you sure?",
            text: "Once deleted, this role cannot be restored!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + roleId).submit();
            }
        });
    }
</script>

@endsection
