@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h4 class="fw-bold mb-4">Dashboard Peserta</h4>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h6>Pendaftaran Aktif</h6>
                    <h2 class="fw-bold mb-0">{{ $totalRegistrations }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark shadow-sm">
                <div class="card-body">
                    <h6>Lomba Tersimpan</h6>
                    <h2 class="fw-bold mb-0">{{ $totalBookmarks }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h6>Notifikasi Baru</h6>
                    <h2 class="fw-bold mb-0">{{ $notifications->where('is_read', false)->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold mb-3">Rekomendasi Lomba Untukmu</h5>
    <div class="row g-3">
        @foreach($recommendations as $rec)
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <span class="badge bg-info text-dark mb-2">{{ $rec->kategori }}</span>
                    <h6 class="fw-bold">{{ $rec->judul }}</h6>
                    <a href="{{ route('lomba.detail', $rec->id) }}" class="btn btn-sm btn-primary mt-2">Lihat Lomba</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection