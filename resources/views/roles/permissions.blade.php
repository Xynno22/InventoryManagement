@extends('layouts.app')

@section('title', 'Manage Permissions for ' . $role->name)

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">


    @if(session('success'))
        <p class="text-green-600 text-center">{{ session('success') }}</p>
    @endif

    <form action="{{ route('roles.permissions.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($permissions as $permission)
            <label class="flex items-center space-x-2 bg-gray-100 p-3 rounded-md shadow-sm hover:bg-gray-200 transition">
                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                    {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                    class="form-checkbox text-blue-600 w-5 h-5">
                <span class="text-gray-700 font-medium">{{ ucwords(str_replace('_', ' ', $permission->name)) }}</span>
            </label>
            @endforeach
        </div>

        <button type="submit" 
            class="mt-6 w-full sm:w-auto bg-green-600 text-white px-6 py-2 rounded-md shadow-md hover:bg-green-700 transition block mx-auto">
            Update Permissions
        </button>
    </form>

    <div class="text-center mt-4">
        <a href="{{ route('roles.index') }}" class="text-blue-600 hover:underline">Back to Roles</a>
    </div>
</div>
@endsection
