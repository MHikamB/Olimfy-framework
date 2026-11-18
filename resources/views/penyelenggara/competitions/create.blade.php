@extends('layouts.app')

@section('title', 'Tambah Lomba')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Tambah Lomba</h1>
            <p class="text-body-secondary mb-0">Publikasikan kompetisi baru untuk peserta OLIMFY.</p>
        </div>
        <a href="{{ route('penyelenggara.lomba.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
            Kembali
        </a>
    </div>

    @include('penyelenggara.competitions._form', [
        'action' => route('penyelenggara.lomba.store'),
        'method' => 'POST',
        'competition' => null,
        'submitLabel' => 'Publikasikan Lomba',
    ])
@endsection
