@extends('layouts.app')

@section('title', 'Manajemen Lomba')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Manajemen Lomba</h1>
            <p class="text-body-secondary mb-0">Kelola kompetisi yang Anda selenggarakan.</p>
        </div>
        <a href="{{ route('penyelenggara.lomba.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>
            Tambah Lomba
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h2 class="card-title h6 mb-0 fw-bold">
                    <i class="bi bi-trophy me-2" aria-hidden="true"></i>
                    Daftar Lomba
                </h2>

                <form method="GET" action="{{ route('penyelenggara.lomba.index') }}" class="d-flex gap-2">
                    <label for="q" class="visually-hidden">Cari lomba</label>
                    <input type="search"
                           id="q"
                           name="q"
                           value="{{ $search }}"
                           class="form-control form-control-sm"
                           placeholder="Cari judul atau kategori..."
                           style="min-width: 240px;">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-search me-1" aria-hidden="true"></i>
                        Cari
                    </button>
                    @if ($search !== '')
                        <a href="{{ route('penyelenggara.lomba.index') }}" class="btn btn-sm btn-outline-secondary">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($competitions->isEmpty())
                <div class="p-5 text-center text-body-secondary">
                    <i class="bi bi-inbox fs-1 d-block mb-2" aria-hidden="true"></i>
                    @if ($search !== '')
                        <p class="mb-3">Tidak ada lomba yang cocok dengan "{{ $search }}".</p>
                        <a href="{{ route('penyelenggara.lomba.index') }}" class="btn btn-outline-secondary btn-sm">
                            Tampilkan semua lomba
                        </a>
                    @else
                        <p class="mb-3">Anda belum mempublikasikan lomba.</p>
                        <a href="{{ route('penyelenggara.lomba.create') }}" class="btn btn-outline-primary btn-sm">
                            Publikasikan Lomba Pertama
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Poster</th>
                                <th>Lomba</th>
                                <th>Biaya</th>
                                <th>Kuota</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($competitions as $competition)
                                <tr>
                                    <td class="ps-3">
                                        @if ($competition->poster_path)
                                            <img src="{{ asset($competition->poster_path) }}"
                                                 alt="Poster {{ $competition->judul }}"
                                                 class="rounded border"
                                                 style="width: 56px; height: 56px; object-fit: cover;">
                                        @else
                                            <span class="text-body-secondary small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $competition->judul }}</div>
                                        <div class="small text-body-secondary">
                                            {{ $competition->kategori }} &middot;
                                            {{ $competition->jenjang }} &middot;
                                            {{ $competition->mode_pelaksanaan }}
                                        </div>
                                    </td>
                                    <td>{{ formatRupiah($competition->biaya_pendaftaran) }}</td>
                                    <td>{{ $competition->kuota_terisi }} / {{ $competition->kuota }}</td>
                                    <td>{{ formatTanggalIndo($competition->deadline_pendaftaran) }}</td>
                                    <td>
                                        <span class="badge {{ $competition->status === 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $competition->status }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('penyelenggara.lomba.edit', $competition->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>
                                            Edit
                                        </a>
                                        <form method="POST"
                                              action="{{ route('penyelenggara.lomba.destroy', $competition->id) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Hapus lomba ini beserta data pendaftarannya?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash me-1" aria-hidden="true"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($competitions->hasPages())
            <div class="card-footer bg-body">
                {{ $competitions->links() }}
            </div>
        @endif
    </div>
@endsection
