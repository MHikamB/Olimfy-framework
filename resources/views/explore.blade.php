@extends('layouts.guest')

@section('content')
<div class="container my-5">
    <div class="row g-4">
        <!-- Kolom Kiri: Sidebar Filter -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm p-3">
                <h5 class="fw-bold mb-3"><i class="bi bi-funnel me-1"></i> Filter Lomba</h5>
                <form action="{{ route('explore') }}" method="GET">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kata Kunci</label>
                        <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control form-control-sm" placeholder="Nama lomba...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kategori</label>
                        @foreach(['Teknologi', 'Desain', 'Bisnis', 'Sains'] as $kat)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="kategori[]" value="{{ $kat }}" id="kat_{{ $kat }}"
                                {{ in_array($kat, (array) request('kategori')) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="kat_{{ $kat }}">{{ $kat }}</label>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jenjang Pendidikan</label>
                        @foreach(['SMA/SMK', 'Mahasiswa', 'Umum'] as $jen)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="jenjang" value="{{ $jen }}" id="jen_{{ $jen }}"
                                {{ request('jenjang') == $jen ? 'checked' : '' }}>
                            <label class="form-check-label small" for="jen_{{ $jen }}">{{ $jen }}</label>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mode Pelaksanaan</label>
                        <select name="mode" class="form-select form-select-sm">
                            <option value="">Semua Mode</option>
                            <option value="Online" {{ request('mode') == 'Online' ? 'selected' : '' }}>Online</option>
                            <option value="Offline" {{ request('mode') == 'Offline' ? 'selected' : '' }}>Offline</option>
                            <option value="Hybrid" {{ request('mode') == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Urutkan</label>
                        <select name="sort" class="form-select form-select-sm">
                            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                            <option value="deadline" {{ request('sort') == 'deadline' ? 'selected' : '' }}>Deadline Terdekat</option>
                            <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Biaya Termurah</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
                        <a href="{{ route('explore') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kolom Kanan: Grid Kompetisi -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">Katalog Kompetisi</h4>
                <span class="text-muted small">Menampilkan {{ $competitions->total() }} lomba</span>
            </div>

            <div class="row g-4">
                @forelse($competitions as $comp)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary">{{ $comp->kategori }}</span>
                                @auth
                                <form action="{{ route('bookmarks.toggle', $comp->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light border-0 text-danger" title="Simpan Lomba">
                                        <i class="bi {{ auth()->user()->bookmarkedCompetitions->contains($comp->id) ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i>
                                    </button>
                                </form>
                                @endauth
                            </div>
                            <h6 class="fw-bold">{{ $comp->judul }}</h6>
                            <p class="text-muted small mb-2"><i class="bi bi-building me-1"></i>{{ $comp->organizer->name ?? 'Penyelenggara' }}</p>
                            <p class="small text-secondary">{{ Str::limit($comp->deskripsi, 75) }}</p>

                            <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary small">
                                    {{ $comp->biaya == 0 ? 'GRATIS' : 'Rp ' . number_format($comp->biaya, 0, ',', '.') }}
                                </span>
                                <a href="{{ route('lomba.detail', $comp->id) }}" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Tidak ada kompetisi yang sesuai dengan filter pencarianmu.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $competitions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection