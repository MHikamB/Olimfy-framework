@extends('layouts.app')

@section('title', 'Dashboard Penyelenggara')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">Dashboard Penyelenggara</h1>
            <p class="text-body-secondary mb-0">Ringkasan performa dan aktivitas lomba Anda.</p>
        </div>
        <a href="{{ route('penyelenggara.lomba.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>
            Buat Lomba
        </a>
    </div>

    @php
        $kpis = [
            [
                'label' => 'Total Lomba Aktif',
                'value' => $totalLombaAktif,
                'icon' => 'bi-trophy',
                'color' => 'primary',
            ],
            [
                'label' => 'Total Tim Pendaftar',
                'value' => $totalTimPendaftar,
                'icon' => 'bi-people',
                'color' => 'info',
            ],
            [
                'label' => 'Menunggu Verifikasi',
                'value' => $menungguVerifikasi,
                'icon' => 'bi-hourglass-split',
                'color' => 'warning',
            ],
            [
                'label' => 'Tim Lolos',
                'value' => $timLolos,
                'icon' => 'bi-patch-check',
                'color' => 'success',
            ],
        ];
    @endphp

    <div class="row g-3 mb-4">
        @foreach ($kpis as $kpi)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-body-secondary text-uppercase small fw-semibold">
                                {{ $kpi['label'] }}
                            </div>
                            <div class="fs-3 fw-bold">{{ $kpi['value'] }}</div>
                        </div>
                        <div class="bg-{{ $kpi['color'] }} bg-opacity-10 text-{{ $kpi['color'] }} rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px;">
                            <i class="bi {{ $kpi['icon'] }} fs-4" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="card-title h6 mb-0 fw-bold">
                <i class="bi bi-clock-history me-2" aria-hidden="true"></i>
                5 Kompetisi Terbaru
            </h2>
            <a href="{{ route('penyelenggara.lomba.index') }}" class="btn btn-outline-primary btn-sm">
                Lihat Semua
            </a>
        </div>

        <div class="card-body p-0">
            @if ($competitions->isEmpty())
                <div class="p-5 text-center text-body-secondary">
                    <i class="bi bi-inbox fs-1 d-block mb-2" aria-hidden="true"></i>
                    <p class="mb-3">Anda belum mempublikasikan kompetisi.</p>
                    <a href="{{ route('penyelenggara.lomba.create') }}" class="btn btn-outline-primary btn-sm">
                        Publikasikan Kompetisi Pertama
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Kompetisi</th>
                                <th>Deadline</th>
                                <th>Kuota</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($competitions as $competition)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">{{ $competition->judul }}</div>
                                        <div class="small text-body-secondary">
                                            {{ $competition->kategori }} &middot;
                                            {{ $competition->jenjang }} &middot;
                                            {{ $competition->mode_pelaksanaan }}
                                        </div>
                                    </td>
                                    <td>{{ formatTanggalIndo($competition->deadline_pendaftaran) }}</td>
                                    <td>{{ $competition->kuota_terisi }} / {{ $competition->kuota }}</td>
                                    <td>
                                        <span class="badge {{ $competition->status === 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $competition->status }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('penyelenggara.lomba.edit', $competition->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-gear me-1" aria-hidden="true"></i>
                                            Kelola
                                        </a>
                                        <a href="{{ route('penyelenggara.validasi.index') }}"
                                           class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-check2-square me-1" aria-hidden="true"></i>
                                            Validasi
                                        </a>
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
