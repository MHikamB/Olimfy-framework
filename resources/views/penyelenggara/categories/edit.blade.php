@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">Edit Kategori</h1>
            <p class="text-body-secondary mb-0">Perbarui informasi kategori kompetisi.</p>
        </div>
        <a href="{{ route('penyelenggara.kategori.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
            Kembali
        </a>
    </div>

    @include('penyelenggara.categories._form', [
        'action' => route('penyelenggara.kategori.update', $category->id),
        'method' => 'PUT',
        'category' => $category,
        'submitLabel' => 'Simpan Perubahan',
    ])
@endsection
