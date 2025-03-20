@extends('layouts.app')

@section('title', 'Manage Admins')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4">Admin Management</h2>

        <!-- Form Tambah Admin -->
        <form action="{{ route('admin.store') }}" method="POST" class="mb-6">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Admin Name</label>
                <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700">Admin Email</label>
                <input type="email" name="email" id="email" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="role" class="block text-gray-700">Assign Role</label>
                <select name="role" id="role" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">Select a Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Admin</button>
        </form>

        <!-- Daftar Admin -->
        <h3 class="text-lg font-semibold mb-4">Admin List</h3>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-4 py-2">Name</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Role</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admins as $admin)
                    <tr class="text-center">
                        <td class="border px-4 py-2">{{ $admin->name }}</td>
                        <td class="border px-4 py-2">{{ $admin->email }}</td>
                        <td class="border px-4 py-2">
                            {{ $admin->roles->pluck('name')->implode(', ') ?: 'No Role Assigned' }}
                        </td>
                        <td class="border px-4 py-2">
                            <!-- Edit Role -->
                            <button type="button" onclick="openEditModal({{ $admin->id }}, '{{ $admin->roles->pluck('name')->implode(', ') }}')"
                                class="text-blue-500 hover:text-blue-700 transition font-medium">
                                Edit Role
                            </button>
                            |
                            <!-- Delete Admin -->
                            <button type="button" onclick="confirmDeleteAdmin(event, '{{ route('admin.destroy', $admin->id) }}')"
                                class="text-red-500 hover:text-red-700 transition font-medium">
                                Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Edit Role -->
    <div id="editRoleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-semibold mb-4">Edit Admin Role</h2>
            <form id="editRoleForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editAdminId">
                <div class="mb-4">
                    <label for="editRole" class="block text-gray-700">Assign Role</label>
                    <select name="role" id="editRole" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">Select a Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Save</button>
                <button type="button" onclick="closeEditModal()" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Cancel</button>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function confirmDeleteAdmin(event, deleteUrl) {
            event.preventDefault();
            Swal.fire({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this admin!",
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
                        body: JSON.stringify({ _method: 'DELETE' })
                    }).then(() => location.reload());
                }
            });
        }

        function openEditModal(adminId, currentRole) {
            document.getElementById('editAdminId').value = adminId;
            document.getElementById('editRole').value = currentRole;
            document.getElementById('editRoleForm').action = `/admin/${adminId}/update-role`;
            document.getElementById('editRoleModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editRoleModal').classList.add('hidden');
        }
    </script>
@endsection
