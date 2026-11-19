@extends('layouts.app')

@section('title', 'Penilaian Karya Peserta')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Penilaian Karya Peserta</h1>
            <p class="text-body-secondary mb-0">Periksa karya yang dikumpulkan peserta dan berikan skor penilaian juri.</p>
        </div>
        <a href="{{ route('penyelenggara.laporan.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-bar-chart-line me-1" aria-hidden="true"></i>
            Lihat Laporan Rekap
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="card-title h6 mb-0 fw-bold">
                <i class="bi bi-award me-2" aria-hidden="true"></i>
                Daftar Karya Masuk ({{ $submissions->count() }})
            </h2>
        </div>

        <div class="card-body p-0">
            @if ($submissions->isEmpty())
                <div class="p-5 text-center text-body-secondary">
                    <i class="bi bi-inbox fs-1 d-block mb-2" aria-hidden="true"></i>
                    <p class="mb-0">Belum ada karya yang dikumpulkan oleh peserta pada kompetisi Anda.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Tim &amp; Kompetisi</th>
                                <th>Judul &amp; Lampiran Karya</th>
                                <th>Status &amp; Skor</th>
                                <th style="min-width: 320px;" class="pe-3">Form Penilaian Juri</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($submissions as $sub)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">{{ $sub->registration?->nama_tim ?? '-' }}</div>
                                        <div class="small text-body-secondary">{{ $sub->competition?->judul ?? '-' }}</div>
                                        <div class="small text-body-secondary mt-1">
                                            Pengirim: {{ $sub->submitter?->name ?? '-' }}
                                            &middot; {{ formatTanggalIndo($sub->submitted_at) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $sub->judul_karya }}</div>
                                        <p class="small text-body-secondary mb-2">{{ Str::limit($sub->deskripsi_karya, 110) }}</p>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if ($sub->link_karya)
                                                <a href="{{ $sub->link_karya }}" target="_blank" rel="noopener" class="badge bg-info text-dark text-decoration-none">
                                                    <i class="bi bi-link-45deg me-1" aria-hidden="true"></i>
                                                    Tautan Demo
                                                </a>
                                            @endif
                                            @if ($sub->file_karya)
                                                <a href="{{ asset('storage/' . $sub->file_karya) }}" target="_blank" rel="noopener" class="badge bg-secondary text-decoration-none">
                                                    <i class="bi bi-file-earmark-arrow-down me-1" aria-hidden="true"></i>
                                                    Unduh Berkas
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="mb-1">{!! statusBadge($sub->status) !!}</div>
                                        @if ($sub->nilai !== null)
                                            <div class="fw-bold text-primary fs-6">Skor: {{ $sub->nilai }}/100</div>
                                            <div class="small text-body-secondary">
                                                Dinilai oleh: {{ $sub->grader?->name ?? '-' }}
                                            </div>
                                        @else
                                            <span class="text-body-secondary small">Belum dinilai</span>
                                        @endif
                                    </td>
                                    <td class="pe-3">
                                        <form method="POST" action="{{ route('penyelenggara.submission.grade', $sub->id) }}" class="row g-2 align-items-center">
                                            @csrf
                                            @method('PATCH')
                                            <div class="col-4">
                                                <input
                                                    type="number"
                                                    name="nilai"
                                                    min="0"
                                                    max="100"
                                                    class="form-control form-control-sm"
                                                    value="{{ $sub->nilai }}"
                                                    placeholder="Nilai (0-100)"
                                                    required
                                                >
                                            </div>
                                            <div class="col-4">
                                                <select name="status" class="form-select form-select-sm" required>
                                                    <option value="dinilai" {{ $sub->status === 'dinilai' ? 'selected' : '' }}>Dinilai</option>
                                                    <option value="finalis" {{ $sub->status === 'finalis' ? 'selected' : '' }}>Finalis</option>
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <button type="submit" class="btn btn-sm btn-success w-100">
                                                    <i class="bi bi-check-lg me-1" aria-hidden="true"></i>
                                                    Simpan
                                                </button>
                                            </div>
                                            <div class="col-12">
                                                <input
                                                    type="text"
                                                    name="catatan_juri"
                                                    class="form-control form-control-sm"
                                                    value="{{ $sub->catatan_juri }}"
                                                    placeholder="Catatan / feedback juri (opsional)"
                                                >
                                            </div>
                                        </form>
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
