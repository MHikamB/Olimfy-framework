@php
    $competition = $competition ?? null;
@endphp

@if ($errors->any())
    <div class="alert alert-danger mb-4" role="alert">
        <h2 class="h6 fw-bold mb-2">
            <i class="bi bi-exclamation-circle-fill me-1" aria-hidden="true"></i>
            Periksa kembali isian formulir Anda:
        </h2>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <div class="card card-primary card-outline mb-4 shadow-sm">
        <div class="card-header">
            <h2 class="card-title h6 mb-0 fw-bold">
                <i class="bi bi-trophy me-2" aria-hidden="true"></i>
                Informasi Lomba
            </h2>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <label for="judul" class="form-label fw-semibold">
                        Judul Lomba <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           id="judul"
                           name="judul"
                           class="form-control @error('judul') is-invalid @enderror"
                           value="{{ old('judul', $competition?->judul) }}"
                           placeholder="Contoh: Olimpiade Teknologi Nasional 2027"
                           required>
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="kategori" class="form-label fw-semibold">
                        Kategori <span class="text-danger">*</span>
                    </label>
                    <select id="kategori"
                            name="kategori"
                            class="form-select @error('kategori') is-invalid @enderror"
                            required>
                        @foreach (['Teknologi', 'Desain', 'Bisnis', 'Sains'] as $kategori)
                            <option value="{{ $kategori }}" @selected(old('kategori', $competition?->kategori) === $kategori)>
                                {{ $kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="jenjang" class="form-label fw-semibold">
                        Jenjang <span class="text-danger">*</span>
                    </label>
                    <select id="jenjang"
                            name="jenjang"
                            class="form-select @error('jenjang') is-invalid @enderror"
                            required>
                        @foreach (['Mahasiswa', 'SMA', 'Umum'] as $jenjang)
                            <option value="{{ $jenjang }}" @selected(old('jenjang', $competition?->jenjang) === $jenjang)>
                                {{ $jenjang }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenjang')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="mode_pelaksanaan" class="form-label fw-semibold">
                        Mode Pelaksanaan <span class="text-danger">*</span>
                    </label>
                    <select id="mode_pelaksanaan"
                            name="mode_pelaksanaan"
                            class="form-select @error('mode_pelaksanaan') is-invalid @enderror"
                            required>
                        @foreach (['Online', 'Offline', 'Hybrid'] as $mode)
                            <option value="{{ $mode }}" @selected(old('mode_pelaksanaan', $competition?->mode_pelaksanaan) === $mode)>
                                {{ $mode }}
                            </option>
                        @endforeach
                    </select>
                    @error('mode_pelaksanaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <h2 class="card-title h6 mb-0 fw-bold">
                <i class="bi bi-calendar2-check me-2" aria-hidden="true"></i>
                Pendaftaran &amp; Poster
            </h2>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="biaya_pendaftaran" class="form-label fw-semibold">
                        Biaya Pendaftaran (Rp) <span class="text-danger">*</span>
                    </label>
                    <input type="number"
                           id="biaya_pendaftaran"
                           name="biaya_pendaftaran"
                           class="form-control @error('biaya_pendaftaran') is-invalid @enderror"
                           value="{{ old('biaya_pendaftaran', $competition?->biaya_pendaftaran ?? 0) }}"
                           min="0"
                           step="1"
                           required>
                    <div class="form-text">Isi 0 jika lomba gratis.</div>
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
                           value="{{ old('kuota', $competition?->kuota ?? 50) }}"
                           min="1"
                           step="1"
                           required>
                    @error('kuota')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="deadline_pendaftaran" class="form-label fw-semibold">
                        Batas Pendaftaran <span class="text-danger">*</span>
                    </label>
                    <input type="date"
                           id="deadline_pendaftaran"
                           name="deadline_pendaftaran"
                           class="form-control @error('deadline_pendaftaran') is-invalid @enderror"
                           value="{{ old('deadline_pendaftaran', $competition?->deadline_pendaftaran?->format('Y-m-d')) }}"
                           min="{{ now()->addDay()->format('Y-m-d') }}"
                           required>
                    @error('deadline_pendaftaran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="poster" class="form-label fw-semibold">Poster Lomba</label>
                    <input type="file"
                           id="poster"
                           name="poster"
                           class="form-control @error('poster') is-invalid @enderror"
                           accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                    <div class="form-text">Opsional. Format JPEG/JPG/PNG, maksimal 2 MB.</div>
                    @error('poster')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if ($competition?->poster_path)
                        <div class="mt-3">
                            <div class="text-body-secondary small mb-2">Poster saat ini:</div>
                            <img src="{{ asset($competition->poster_path) }}"
                                 alt="Poster {{ $competition->judul }}"
                                 class="rounded border"
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <h2 class="card-title h6 mb-0 fw-bold">
                <i class="bi bi-text-paragraph me-2" aria-hidden="true"></i>
                Deskripsi Lomba
            </h2>
        </div>
        <div class="card-body">
            <label for="deskripsi" class="form-label fw-semibold">
                Deskripsi <span class="text-danger">*</span>
            </label>
            <textarea id="deskripsi"
                      name="deskripsi"
                      rows="6"
                      class="form-control @error('deskripsi') is-invalid @enderror"
                      placeholder="Jelaskan tema, ketentuan, dan informasi penting lomba."
                      required>{{ old('deskripsi', $competition?->deskripsi) }}</textarea>
            @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('penyelenggara.lomba.index') }}" class="btn btn-outline-secondary">
            Batal
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1" aria-hidden="true"></i>
            {{ $submitLabel }}
        </button>
    </div>
</form>
