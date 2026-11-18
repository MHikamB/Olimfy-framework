@extends('layouts.app')

@section('title', 'Dashboard Peserta')

@section('content')
    <div class="card">
        <div class="card-body">
            <h1 class="h4 mb-2">Dashboard Peserta</h1>
            <p class="text-body-secondary mb-0">
                Selamat datang, {{ Auth::user()->name }}. Jelajahi lomba dan pantau pendaftaran Anda dari menu sidebar.
            </p>
        </div>
    </div>
@endsection
