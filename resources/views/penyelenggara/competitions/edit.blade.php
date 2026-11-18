@extends('layouts.app')

@section('title', 'Edit Lomba')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Edit Lomba</h1>
            <p class="text-body-secondary mb-0">Perbarui informasi lomba yang sudah dipublikasikan.</p>
        </div>
        <a href="{{ route('penyelenggara.lomba.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
            Kembali
        </a>
    </div>

    @include('penyelenggara.competitions._form', [
        'action' => route('penyelenggara.lomba.update', $competition->id),
        'method' => 'PUT',
        'competition' => $competition,
        'submitLabel' => 'Simpan Perubahan',
    ])
@endsection
