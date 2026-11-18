@extends('layouts.guest')

@section('content')
<!-- Hero Section -->
<section class="bg-primary text-white py-5">
    <div class="container py-4 text-center">
        <h1 class="display-5 fw-bold mb-3">Temukan & Ikuti Kompetisi Terbaik di Indonesia</h1>
        <p class="lead mb-4 mx-auto" style="max-width: 650px;">Platform terpadu untuk mengeksplorasi ratusan lomba akademik, hackathon, desain, dan olimpiade tingkat nasional.</p>
        <form action="{{ route('explore') }}" method="GET" class="mx-auto" style="max-width: 600px;">
            <div class="input-group input-group-lg shadow">
                <input type="text" name="keyword" class="form-control border-0" placeholder="Cari lomba hackathon, UI/UX, karya tulis...">
                <button class="btn btn-warning fw-bold px-4" type="submit"><i class="bi bi-search me-1"></i> Cari</button>
            </div>
        </form>
        <div class="d-flex justify-content-center gap-4 mt-4 small">
            <span><i class="bi bi-trophy me-1"></i> <strong>{{ $totalActiveCompetitions }}</strong> Lomba Aktif</span>
            <span><i class="bi bi-people me-1"></i> <strong>{{ $totalParticipants }}</strong> Pendaftar</span>
        </div>
    </div>
</section>

<!-- 4 Kartu Kategori -->
<section class="container my-5">
    <h3 class="fw-bold mb-4 text-center">Eksplorasi Berdasarkan Kategori</h3>
    <div class="row g-3">
        @php
        $catIcons = ['Teknologi' => 'bi-cpu', 'Desain' => 'bi-palette', 'Bisnis' => 'bi-briefcase', 'Sains' => 'bi-flask'];
        @endphp
        @foreach($categories as $cat)
        <div class="col-6 col-md-3">
            <a href="{{ route('explore', ['kategori[]' => $cat]) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center p-4 h-100 hover-shadow">
                    <i class="bi {{ $catIcons[$cat] ?? 'bi-star' }} fs-1 text-primary mb-2"></i>
                    <h5 class="fw-bold text-dark mb-0">{{ $cat }}</h5>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</section>

<!-- Kompetisi Unggulan -->
<section class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Kompetisi Unggulan</h3>
        <a href="{{ route('explore') }}" class="btn btn-outline-primary btn-sm">Lihat Semua <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-4">
        @foreach($popularCompetitions as $comp)
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="mb-2">
                        <span class="badge bg-primary-subtle text-primary">{{ $comp->kategori }}</span>
                        <span class="badge bg-secondary-subtle text-secondary">{{ $comp->jenjang }}</span>
                    </div>
                    <h5 class="card-title fw-bold">{{ $comp->judul }}</h5>
                    <p class="text-muted small mb-3">{{ Str::limit($comp->deskripsi, 80) }}</p>

                    <div class="mt-auto">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Kuota Terisi</span>
                            <span>{{ $comp->kuota_terisi }}/{{ $comp->kuota }}</span>
                        </div>
                        @php
                        $persen = $comp->kuota > 0 ? round(($comp->kuota_terisi / $comp->kuota) * 100) : 0;
                        @endphp
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar" data-width="{{ $persen }}"></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-success">
                                {{ $comp->biaya == 0 ? 'GRATIS' : (function_exists('formatRupiah') ? formatRupiah($comp->biaya) : 'Rp ' . number_format($comp->biaya, 0, ',', '.')) }}
                            </span>
                            <a href="{{ route('lomba.detail', $comp->id) }}" class="btn btn-primary btn-sm">Detail</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@push('scripts')
<script>
    document.querySelectorAll('.progress-bar[data-width]').forEach(bar => {
        bar.style.width = bar.dataset.width + '%';
    });
</script>
@endpush
@endsection