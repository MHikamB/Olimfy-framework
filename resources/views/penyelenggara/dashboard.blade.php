@extends('layouts.app')

@section('title', 'Dashboard Penyelenggara')

@section('content')
    <div class="card">
        <div class="card-body">
            <h1 class="h4 mb-2">Dashboard Penyelenggara</h1>
            <p class="text-body-secondary mb-0">
                Selamat datang, {{ Auth::user()->name }}. Kelola lomba dan validasi berkas peserta dari menu sidebar.
            </p>
        </div>
    </div>
@endsection
