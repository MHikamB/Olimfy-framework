@extends('layouts.app')

@section('title', 'Laporan Rekap Kompetisi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Laporan &amp; Rekapitulasi Kompetisi</h1>
            <p class="text-body-secondary mb-0">Ringkasan agregat pendaftar, kelulusan berkas, pendapatan lunas, dan rata-rata skor karya.</p>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('penyelenggara.laporan.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="kategori" class="form-label fw-semibold small">Filter Kategori</label>
                    <select id="kategori" name="kategori" class="form-select form-select-sm">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ $selectedKategori === $kat ? 'selected' : '' }}>
                                {{ $kat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="status" class="form-label fw-semibold small">Status Kompetisi</label>
                    <select id="status" name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ $selectedStatus === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Tutup" {{ $selectedStatus === 'Tutup' ? 'selected' : '' }}>Tutup</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-funnel me-1" aria-hidden="true"></i>
                        Terapkan Filter
                    </button>
                    <a href="{{ route('penyelenggara.laporan.index') }}" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white h-100">
                <div class="card-body">
                    <div class="small opacity-75">Jumlah Kompetisi</div>
                    <div class="fs-3 fw-bold">{{ $summary['total_lomba'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-dark h-100">
                <div class="card-body">
                    <div class="small">Total Tim Pendaftar</div>
                    <div class="fs-3 fw-bold">{{ $summary['total_pendaftar'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white h-100">
                <div class="card-body">
                    <div class="small opacity-75">Tim Lolos Verifikasi</div>
                    <div class="fs-3 fw-bold">{{ $summary['total_lolos'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="small">Total Pendapatan Lunas</div>
                    <div class="fs-4 fw-bold">{{ formatRupiah($summary['total_pendapatan']) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Rekap Per Kompetisi --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h2 class="card-title h6 mb-0 fw-bold">
                <i class="bi bi-table me-2" aria-hidden="true"></i>
                Rekapitulasi Per Kompetisi
            </h2>
        </div>
        <div class="card-body p-0">
            @if ($competitionReports->isEmpty())
                <div class="p-4 text-center text-body-secondary">
                    Belum ada data kompetisi yang sesuai dengan filter laporan.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Kompetisi</th>
                                <th>Kategori &amp; Jenjang</th>
                                <th class="text-center">Kuota</th>
                                <th class="text-center">Total Pendaftar</th>
                                <th class="text-center">Lolos</th>
                                <th class="text-center">Ditolak</th>
                                <th class="text-center">Rata-rata Nilai</th>
                                <th class="text-end pe-3">Pendapatan Lunas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($competitionReports as $row)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">{{ $row->judul }}</div>
                                        <span class="badge {{ $row->status === 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $row->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $row->kategori }}</span>
                                        <span class="badge bg-light text-dark border">{{ $row->jenjang }}</span>
                                    </td>
                                    <td class="text-center">{{ $row->kuota_terisi }} / {{ $row->kuota }}</td>
                                    <td class="text-center fw-semibold">{{ $row->total_pendaftar }}</td>
                                    <td class="text-center text-success fw-semibold">{{ $row->pendaftar_lolos }}</td>
                                    <td class="text-center text-danger">{{ $row->pendaftar_ditolak }}</td>
                                    <td class="text-center">
                                        {{ $row->rata_rata_nilai !== null ? number_format((float) $row->rata_rata_nilai, 1) : '-' }}
                                    </td>
                                    <td class="text-end pe-3 fw-bold text-primary">
                                        {{ formatRupiah($row->total_pendapatan) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Tabel Rekap Per Kategori --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="card-title h6 mb-0 fw-bold">
                <i class="bi bi-pie-chart-fill me-2" aria-hidden="true"></i>
                Rekapitulasi Per Kategori
            </h2>
        </div>
        <div class="card-body p-0">
            @if ($categoryReports->isEmpty())
                <div class="p-4 text-center text-body-secondary">Belum ada data kategori.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Kategori</th>
                                <th class="text-center">Jumlah Lomba</th>
                                <th class="text-center">Total Pendaftar</th>
                                <th class="text-end pe-3">Total Pendapatan Lunas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categoryReports as $cat)
                                <tr>
                                    <td class="ps-3 fw-semibold">{{ $cat->kategori }}</td>
                                    <td class="text-center">{{ $cat->jumlah_lomba }}</td>
                                    <td class="text-center">{{ $cat->total_pendaftar }}</td>
                                    <td class="text-end pe-3 fw-semibold">{{ formatRupiah($cat->total_pendapatan) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
