@extends('layouts.app')

@section('title', 'Master Kategori')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">Master Kategori</h1>
            <p class="text-body-secondary mb-0">
                Kelola kategori yang dipakai pada kompetisi OLIMFY.
            </p>
        </div>
        <a href="{{ route('penyelenggara.kategori.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>
            Tambah Kategori
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h2 class="card-title h6 mb-0 fw-bold">
                    <i class="bi bi-tags me-2" aria-hidden="true"></i>
                    Daftar Kategori
                </h2>

                <form method="GET" action="{{ route('penyelenggara.kategori.index') }}" class="d-flex gap-2">
                    <label for="q" class="visually-hidden">Cari kategori</label>
                    <input type="search"
                           id="q"
                           name="q"
                           value="{{ $search }}"
                           class="form-control form-control-sm"
                           placeholder="Cari nama atau deskripsi..."
                           style="min-width: 240px;">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-search me-1" aria-hidden="true"></i>
                        Cari
                    </button>
                    @if ($search !== '')
                        <a href="{{ route('penyelenggara.kategori.index') }}" class="btn btn-sm btn-outline-secondary">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($categories->isEmpty())
                <div class="p-5 text-center text-body-secondary">
                    <i class="bi bi-inbox fs-1 d-block mb-2" aria-hidden="true"></i>
                    @if ($search !== '')
                        <p class="mb-3">Tidak ada kategori yang cocok dengan "{{ $search }}".</p>
                        <a href="{{ route('penyelenggara.kategori.index') }}" class="btn btn-outline-secondary btn-sm">
                            Tampilkan semua kategori
                        </a>
                    @else
                        <p class="mb-3">Belum ada kategori yang terdaftar.</p>
                        <a href="{{ route('penyelenggara.kategori.create') }}" class="btn btn-outline-primary btn-sm">
                            Tambah Kategori Pertama
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 60px;">#</th>
                                <th>Kategori</th>
                                <th>Slug</th>
                                <th>Deskripsi</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td class="ps-3 text-body-secondary">
                                        {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                                  style="width: 36px; height: 36px;">
                                                <i class="bi {{ $category->ikon }}" aria-hidden="true"></i>
                                            </span>
                                            <span class="fw-semibold">{{ $category->nama_kategori }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <code>{{ $category->slug }}</code>
                                    </td>
                                    <td class="text-body-secondary">
                                        {{ \Illuminate\Support\Str::limit($category->deskripsi, 80) ?: '-' }}
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('penyelenggara.kategori.edit', $category->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>
                                            Edit
                                        </a>
                                        <form method="POST"
                                              action="{{ route('penyelenggara.kategori.destroy', $category->id) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Hapus kategori ini?');">
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

        @if ($categories->hasPages())
            <div class="card-footer bg-body">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
@endsection
