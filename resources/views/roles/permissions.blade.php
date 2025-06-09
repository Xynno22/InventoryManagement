@extends('layouts.app')

@section('title', 'Manage Permissions')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-4 text-center text-gray-800">
        Manage Permission
    </h2>

    @if(session('success'))
        <p class="text-green-600 text-center font-semibold mb-4">{{ session('success') }}</p>
    @endif

    <form action="{{ route('roles.permissions.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Scrollable permissions list -->
        <div class="h-64 overflow-y-auto border border-gray-200 rounded-md p-4 mb-6 bg-gray-50 shadow-inner">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($permissions as $permission)
                <label class="flex items-center space-x-2 bg-white p-3 rounded-md shadow-sm hover:bg-gray-100 transition border border-gray-200">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                        {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                        class="form-checkbox text-blue-600 w-5 h-5">
                    <span class="text-gray-700 font-medium">{{ ucwords(str_replace('_', ' ', $permission->name)) }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-center sm:justify-between items-center gap-4">
            <a href="{{ route('roles.index') }}"
                class="text-blue-600 hover:underline text-sm">
                ← Back to Roles
            </a>

            <button type="submit" 
                class="bg-green-600 text-white px-6 py-2 rounded-md shadow-md hover:bg-green-700 transition">
                Update Permissions
            </button>
        </div>
    </form>
</div>
@endsection
