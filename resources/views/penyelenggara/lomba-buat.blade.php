@extends('layouts.app')

@section('title', 'Buat Lomba')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">Buat Lomba Baru</h1>
            <p class="text-body-secondary mb-0">Lengkapi informasi kompetisi yang akan dipublikasikan.</p>
        </div>
        <a href="{{ route('penyelenggara.lomba.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
            Kembali
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="card-title h6 mb-0 fw-bold">
                <i class="bi bi-trophy me-2" aria-hidden="true"></i>
                Formulir Kompetisi
            </h2>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger mb-4" role="alert">
                    <h3 class="h6 fw-bold mb-2">
                        <i class="bi bi-exclamation-circle-fill me-1" aria-hidden="true"></i>
                        Periksa kembali isian formulir Anda:
                    </h3>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('penyelenggara.lomba.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-12">
                        <label for="judul" class="form-label fw-semibold">
                            Judul <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="judul"
                               name="judul"
                               class="form-control @error('judul') is-invalid @enderror"
                               value="{{ old('judul') }}"
                               placeholder="Contoh: Olimpiade Teknologi Nasional 2027"
                               required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="kategori" class="form-label fw-semibold">
                            Kategori <span class="text-danger">*</span>
                        </label>
                        <select id="kategori"
                                name="kategori"
                                class="form-select @error('kategori') is-invalid @enderror"
                                required>
                            <option value="" disabled @selected(old('kategori') === null)>Pilih kategori...</option>
                            @foreach (['Teknologi', 'Desain', 'Bisnis', 'Sains'] as $kategori)
                                <option value="{{ $kategori }}" @selected(old('kategori') === $kategori)>
                                    {{ $kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="jenjang" class="form-label fw-semibold">
                            Jenjang <span class="text-danger">*</span>
                        </label>
                        <select id="jenjang"
                                name="jenjang"
                                class="form-select @error('jenjang') is-invalid @enderror"
                                required>
                            <option value="" disabled @selected(old('jenjang') === null)>Pilih jenjang...</option>
                            @foreach (['Mahasiswa', 'SMA', 'Umum'] as $jenjang)
                                <option value="{{ $jenjang }}" @selected(old('jenjang') === $jenjang)>
                                    {{ $jenjang }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenjang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="form-label fw-semibold">
                            Mode Pelaksanaan <span class="text-danger">*</span>
                        </div>
                        <div class="d-flex flex-wrap gap-4">
                            @foreach (['Online', 'Offline', 'Hybrid'] as $mode)
                                <div class="form-check">
                                    <input type="radio"
                                           id="mode_{{ strtolower($mode) }}"
                                           name="mode_pelaksanaan"
                                           value="{{ $mode }}"
                                           class="form-check-input @error('mode_pelaksanaan') is-invalid @enderror"
                                           @checked(old('mode_pelaksanaan', 'Online') === $mode)
                                           required>
                                    <label class="form-check-label" for="mode_{{ strtolower($mode) }}">
                                        {{ $mode }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('mode_pelaksanaan')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="biaya_pendaftaran" class="form-label fw-semibold">
                            Biaya Pendaftaran (Rp) <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               id="biaya_pendaftaran"
                               name="biaya_pendaftaran"
                               class="form-control @error('biaya_pendaftaran') is-invalid @enderror"
                               value="{{ old('biaya_pendaftaran', 0) }}"
                               min="0"
                               step="1"
                               required>
                        <div class="form-text">Isi 0 jika kompetisi gratis.</div>
                        @error('biaya_pendaftaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="kuota" class="form-label fw-semibold">
                            Kuota Tim <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                               id="kuota"
                               name="kuota"
                               class="form-control @error('kuota') is-invalid @enderror"
                               value="{{ old('kuota', 50) }}"
                               min="1"
                               step="1"
                               required>
                        @error('kuota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="deadline_pendaftaran" class="form-label fw-semibold">
                            Deadline Pendaftaran <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               id="deadline_pendaftaran"
                               name="deadline_pendaftaran"
                               class="form-control @error('deadline_pendaftaran') is-invalid @enderror"
                               value="{{ old('deadline_pendaftaran') }}"
                               min="{{ now()->addDay()->format('Y-m-d') }}"
                               required>
                        @error('deadline_pendaftaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="poster" class="form-label fw-semibold">
                            Upload Poster
                        </label>
                        <input type="file"
                               id="poster"
                               name="poster"
                               class="form-control @error('poster') is-invalid @enderror"
                               accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                        <div class="form-text">Opsional. Format JPEG/JPG/PNG, maksimal 2 MB.</div>
                        @error('poster')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="deskripsi" class="form-label fw-semibold">
                            Deskripsi <span class="text-danger">*</span>
                        </label>
                        <textarea id="deskripsi"
                                  name="deskripsi"
                                  rows="6"
                                  class="form-control @error('deskripsi') is-invalid @enderror"
                                  placeholder="Jelaskan tema, ketentuan, dan informasi penting kompetisi."
                                  required>{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('penyelenggara.lomba.index') }}" class="btn btn-outline-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-megaphone me-1" aria-hidden="true"></i>
                        Publikasikan Kompetisi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
