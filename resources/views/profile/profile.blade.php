@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg">

        <!-- Success Alert -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center justify-between" role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.style.display='none';" class="text-green-700 hover:text-green-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        <div class="flex items-center space-x-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">{{ $company->name }}</h2>
                <p class="text-gray-600">{{ $company->email }}</p>
            </div>
        </div>

        <div class="mt-6 space-y-4">
            <div class="flex justify-between items-center border-b pb-2">
                <span class="text-gray-600">Nama:</span>
                <span class="text-gray-900 font-medium">{{ $company->name }}</span>
            </div>
            <div class="flex justify-between items-center border-b pb-2">
                <span class="text-gray-600">Email:</span>
                <span class="text-gray-900 font-medium">{{ $company->email }}</span>
            </div>
            <div class="flex justify-between items-center border-b pb-2">
                <span class="text-gray-600">Telepon:</span>
                <span class="text-gray-900 font-medium">{{ $company->phone ?? '-' }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Alamat:</span>
                <span class="text-gray-900 font-medium">{{ $company->address ?? '-' }}</span>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <a href="{{ route('profile.edit') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500">
                Edit Profile
            </a>
        </div>
    </div>
@endsection
