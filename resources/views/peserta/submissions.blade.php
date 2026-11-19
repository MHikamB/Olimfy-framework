@extends('layouts.app')

@section('title', 'Pengumpulan Karya')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Pengumpulan Karya Lomba</h1>
            <p class="text-body-secondary mb-0">Unggah karya untuk pendaftaran tim yang sudah lolos verifikasi berkas dan lunas pembayaran.</p>
        </div>
        <a href="{{ route('peserta.pendaftaran.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-journal-check me-1" aria-hidden="true"></i>
            Lihat Pendaftaran Saya
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="card-title h6 mb-0 fw-bold">
                <i class="bi bi-cloud-arrow-up-fill me-2" aria-hidden="true"></i>
                Daftar Tim Memenuhi Syarat Submission
            </h2>
        </div>

        <div class="card-body p-0">
            @if ($registrations->isEmpty())
                <div class="p-5 text-center text-body-secondary">
                    <i class="bi bi-folder-x fs-1 d-block mb-2" aria-hidden="true"></i>
                    <p class="mb-3">Belum ada pendaftaran yang berstatus <strong>Lolos Seleksi</strong> dan <strong>Lunas</strong>.</p>
                    <a href="{{ route('peserta.pendaftaran.index') }}" class="btn btn-outline-primary btn-sm">
                        Periksa Status Pendaftaran
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Tim &amp; Kompetisi</th>
                                <th>Judul Karya</th>
                                <th>Status Karya</th>
                                <th>Nilai Juri</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registrations as $index => $reg)
                                <tr>
                                    <td class="ps-3">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $reg->nama_tim }}</div>
                                        <div class="small text-body-secondary">{{ $reg->competition?->judul ?? '-' }}</div>
                                    </td>
                                    <td>
                                        @if ($reg->submission)
                                            <div class="fw-semibold">{{ $reg->submission->judul_karya }}</div>
                                            <div class="small text-body-secondary">
                                                Dikirim: {{ formatTanggalIndo($reg->submission->submitted_at) }}
                                            </div>
                                        @else
                                            <span class="text-body-secondary small">Belum ada karya dikirim</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($reg->submission)
                                            {!! statusBadge($reg->submission->status) !!}
                                        @else
                                            <span class="badge bg-secondary">Belum Mengumpulkan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($reg->submission && $reg->submission->nilai !== null)
                                            <span class="fw-bold text-primary">{{ $reg->submission->nilai }}/100</span>
                                            @if ($reg->submission->catatan_juri)
                                                <div class="small text-body-secondary">{{ $reg->submission->catatan_juri }}</div>
                                            @endif
                                        @else
                                            <span class="text-body-secondary small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        @if (! $reg->submission || $reg->submission->status === 'dikirim')
                                            <a href="{{ route('peserta.submission.create', $reg->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-upload me-1" aria-hidden="true"></i>
                                                {{ $reg->submission ? 'Perbarui Karya' : 'Kumpulkan Karya' }}
                                            </a>
                                        @else
                                            <span class="badge bg-light text-dark border">Terkunci (Sudah Dinilai)</span>
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
