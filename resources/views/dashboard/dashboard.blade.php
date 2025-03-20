@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    Selamat Datang di Dashboard
@endsection

@section('content')

{{-- @php
    dd(Auth::user()->can('create category'),
    Auth::user()->can('update category'),
    Auth::user()->can('view category'),
    Auth::user()->can('delete category')
    );
@endphp --}}
    <p>Ini adalah halaman utama Dashboard Anda. 🎉</p>
@endsection
