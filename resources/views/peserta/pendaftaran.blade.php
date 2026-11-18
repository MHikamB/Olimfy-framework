@extends('layouts.app')

@section('title', 'Pendaftaran Saya')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Pendaftaran Saya</h1>
            <p class="text-body-secondary mb-0">Pantau status kurasi berkas, riwayat tim, dan tagihan pembayaran lomba Anda.</p>
        </div>
        <a href="{{ url('/explore') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-search me-1" aria-hidden="true"></i>
            Cari Lomba Lain
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="card-title h6 mb-0 fw-bold">
                <i class="bi bi-journal-check me-2" aria-hidden="true"></i>
                Riwayat Pendaftaran Kompetisi
            </h2>
        </div>

        <div class="card-body p-0">
            @if ($registrations->isEmpty())
                <div class="p-5 text-center text-body-secondary">
                    <i class="bi bi-inbox fs-1 d-block mb-2" aria-hidden="true"></i>
                    <p class="mb-3">Anda belum mendaftar ke kompetisi mana pun.</p>
                    <a href="{{ url('/explore') }}" class="btn btn-outline-primary btn-sm">
                        Jelajahi Kompetisi Tersedia
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Nama Tim</th>
                                <th>Kompetisi</th>
                                <th>Tanggal Daftar</th>
                                <th>Berkas</th>
                                <th>Status Verifikasi</th>
                                <th>Pembayaran</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registrations as $index => $reg)
                                <tr>
                                    <td class="ps-3">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $reg->nama_tim }}</div>
                                        <div class="small text-body-secondary">
                                            {{ $reg->members->count() }} anggota
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $reg->competition?->judul ?? '-' }}</div>
                                        <div class="small text-body-secondary">
                                            {{ $reg->competition?->kategori }} &middot; {{ formatRupiah($reg->competition?->biaya_pendaftaran) }}
                                        </div>
                                    </td>
                                    <td>{{ formatTanggalIndo($reg->created_at) }}</td>
                                    <td>
                                        @forelse ($reg->documents as $doc)
                                            <a
                                                href="{{ asset('storage/' . $doc->file_path) }}"
                                                target="_blank"
                                                rel="noopener"
                                                class="badge bg-light text-dark border text-decoration-none me-1"
                                            >
                                                <i class="bi bi-file-earmark-text me-1" aria-hidden="true"></i>
                                                {{ strtoupper(str_replace('_', ' ', $doc->jenis_dokumen)) }}
                                            </a>
                                        @empty
                                            <span class="text-body-secondary small">-</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        {!! statusBadge($reg->status_verifikasi) !!}
                                        @if ($reg->catatan_kurasi)
                                            <div class="small text-body-secondary mt-1">
                                                Catatan: {{ $reg->catatan_kurasi }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($reg->payment)
                                            {!! statusBadge($reg->payment->status_pembayaran) !!}
                                            <div class="small text-body-secondary mt-1">
                                                {{ $reg->payment->kode_invoice }}
                                            </div>
                                        @else
                                            <span class="text-body-secondary small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        @if ($reg->payment)
                                            <a href="{{ route('peserta.invoice.show', $reg->payment->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-receipt me-1" aria-hidden="true"></i>
                                                Lihat Invoice
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
