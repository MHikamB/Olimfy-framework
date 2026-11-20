@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">Tambah Kategori</h1>
            <p class="text-body-secondary mb-0">Daftarkan kategori baru untuk kompetisi OLIMFY.</p>
        </div>
        <a href="{{ route('penyelenggara.kategori.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
            Kembali
        </a>
    </div>

    @include('penyelenggara.categories._form', [
        'action' => route('penyelenggara.kategori.store'),
        'method' => 'POST',
        'category' => null,
        'submitLabel' => 'Simpan Kategori',
    ])
@endsection
