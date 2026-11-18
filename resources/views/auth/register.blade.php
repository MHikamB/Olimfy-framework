@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
    <p class="login-box-msg">Buat akun OLIMFY baru.</p>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input type="text"
                   id="name"
                   name="name"
                   value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror"
                   placeholder="Nama lengkap"
                   required
                   autofocus
                   autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email"
                   id="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="nama@email.com"
                   required
                   autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password"
                   id="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Minimal 8 karakter"
                   required
                   autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password"
                   id="password_confirmation"
                   name="password_confirmation"
                   class="form-control"
                   placeholder="Ulangi password"
                   required
                   autocomplete="new-password">
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Daftar Sebagai</label>
            <select id="role"
                    name="role"
                    class="form-select @error('role') is-invalid @enderror"
                    required>
                <option value="peserta" @selected(old('role', 'peserta') === 'peserta')>Peserta</option>
                <option value="penyelenggara" @selected(old('role') === 'penyelenggara')>Penyelenggara</option>
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-person-plus me-1" aria-hidden="true"></i>
            Daftar
        </button>
    </form>

    <p class="mt-3 mb-0 text-center">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </p>
@endsection
