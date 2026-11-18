@extends('layouts.guest')

@section('content')
<div class="container my-5">
    <div class="row g-4">
        <!-- Konten Utama -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4">
                <div class="mb-3">
                    <span class="badge bg-primary me-1">{{ $competition->kategori }}</span>
                    <span class="badge bg-info text-dark me-1">{{ $competition->jenjang }}</span>
                    <span class="badge bg-secondary">{{ $competition->mode_pelaksanaan }}</span>
                </div>
                <h2 class="fw-bold mb-2">{{ $competition->judul }}</h2>
                <p class="text-muted mb-4"><i class="bi bi-patch-check-fill text-primary me-1"></i> Diselenggarakan oleh <strong>{{ $competition->organizer->name ?? 'Panitia Resmi' }}</strong></p>

                <!-- Tabbed Navigation -->
                <ul class="nav nav-tabs mb-3" id="lombaTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#deskripsi" type="button">Deskripsi Lomba</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#syarat" type="button">Persyaratan & Ketentuan</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#hadiah" type="button">Hadiah & Fasilitas</button>
                    </li>
                </ul>
                <div class="tab-content" id="lombaTabContent">
                    <div class="tab-pane fade show active" id="deskripsi">
                        <p style="white-space: pre-line;">{{ $competition->deskripsi }}</p>
                    </div>
                    <div class="tab-pane fade" id="syarat">
                        <ul>
                            <li>Peserta merupakan pelajar/mahasiswa aktif sesuai jenjang <strong>{{ $competition->jenjang }}</strong>.</li>
                            <li>Melengkapi biodata profil peserta dengan benar sebelum mendaftar.</li>
                            <li>Mengunggah berkas persyaratan sesuai buku panduan lomba.</li>
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="hadiah">
                        <ul>
                            <li>Uang pembinaan total jutaan rupiah bagi para pemenang.</li>
                            <li>E-Sertifikat Nasional untuk seluruh peserta dan finalis.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Informasi Pendaftaran -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 90px;">
                <h5 class="fw-bold mb-3">Informasi Pendaftaran</h5>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Biaya Daftar</span>
                        <span class="fw-bold text-success">{{ $competition->biaya == 0 ? 'GRATIS' : 'Rp ' . number_format($competition->biaya, 0, ',', '.') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Kuota Tersisa</span>
                        <span class="fw-bold">{{ max(0, $competition->kuota - $competition->kuota_terisi) }} Kursi</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Batas Pendaftaran</span>
                        <span class="fw-bold text-danger">{{ \Carbon\Carbon::parse($competition->deadline_pendaftaran)->format('d M Y') }}</span>
                    </li>
                </ul>

                <div class="d-grid gap-2">
                    <a href="{{ url('/peserta/daftar/' . $competition->id) }}" class="btn btn-success btn-lg fw-bold">
                        <i class="bi bi-pencil-square me-1"></i> Daftar Sekarang
                    </a>

                    @auth
                    <form action="{{ route('bookmarks.toggle', $competition->id) }}" method="POST" class="d-grid">
                        @csrf
                        <button type="submit" class="btn {{ $isBookmarked ? 'btn-warning' : 'btn-outline-secondary' }}">
                            <i class="bi {{ $isBookmarked ? 'bi-bookmark-fill' : 'bi-bookmark' }} me-1"></i>
                            {{ $isBookmarked ? 'Tersimpan di Watchlist' : 'Simpan Lomba' }}
                        </button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary">Login untuk Simpan Lomba</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection