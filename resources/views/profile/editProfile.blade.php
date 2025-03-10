@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Edit Profile</h2>

    <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-gray-700 font-medium">Company Name</label>
            <input type="text" name="name" value="{{ $company->name }}" class="w-full px-4 py-2 border rounded focus:ring focus:ring-blue-300">
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-medium">Email</label>
            <input type="email" id="email" name="email" 
                   value="{{ $company->email }}" 
                   class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed" 
                   readonly>
        </div>        

        <div>
            <label class="block text-gray-700 font-medium">Phone Number</label>
            <input type="text" name="phone" value="{{ $company->phone }}" class="w-full px-4 py-2 border rounded focus:ring focus:ring-blue-300">
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Address</label>
            <textarea name="address" class="w-full px-4 py-2 border rounded focus:ring focus:ring-blue-300">{{ $company->address }}</textarea>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('profile.index') }}" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                Cancel
            </a>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                Save
            </button>
        </div>
    </form>
</div>
@endsection
