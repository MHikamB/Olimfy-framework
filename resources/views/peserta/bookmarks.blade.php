@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h4 class="fw-bold mb-4">Watchlist Lomba Favorit</h4>
    <div class="row g-4">
        @forelse($bookmarks as $comp)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <span class="badge bg-primary align-self-start mb-2">{{ $comp->kategori }}</span>
                    <h5 class="fw-bold">{{ $comp->judul }}</h5>
                    <p class="text-muted small">{{ Str::limit($comp->deskripsi, 90) }}</p>
                    <div class="mt-auto d-flex gap-2">
                        <a href="{{ route('lomba.detail', $comp->id) }}" class="btn btn-success btn-sm flex-grow-1">Daftar Sekarang</a>
                        <form action="{{ route('bookmarks.toggle', $comp->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">Hapus dari Simpanan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">Belum ada lomba yang kamu simpan. <a href="{{ route('explore') }}">Jelajahi lomba sekarang!</a></div>
        </div>
        @endforelse
    </div>
</div>
@endsection