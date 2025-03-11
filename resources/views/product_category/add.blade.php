@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg mt-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Add Category</h2>

        @if ($errors->any())
            <div class="bg-red-500 text-white p-3 rounded-md mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
                <input type="text" name="name"
                    class="w-full px-4 py-2 border border-gray-600 rounded-md ">
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('categories.index') }}"
                    class="bg-red-500 text-white px-5 py-2 rounded-lg hover:bg-red-600 transition">
                    Cancel
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Save
                </button>
            </div>
        </form>
    </div>
@endsection
