@extends('layouts.app')

@section('title', 'Unggah Karya Tim')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Formulir Pengumpulan Karya</h1>
            <p class="text-body-secondary mb-0">
                Tim <strong>{{ $registration->nama_tim }}</strong> &middot; {{ $registration->competition?->judul ?? '-' }}
            </p>
        </div>
        <a href="{{ route('peserta.submission.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4" role="alert">
            <h2 class="h6 fw-bold mb-2">
                <i class="bi bi-exclamation-circle-fill me-1" aria-hidden="true"></i>
                Periksa kembali isian karya Anda:
            </h2>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header">
            <h2 class="card-title h6 mb-0 fw-bold">
                <i class="bi bi-file-earmark-code-fill me-2" aria-hidden="true"></i>
                Detail Karya Lomba
            </h2>
        </div>

        <form method="POST" action="{{ route('peserta.submission.store', $registration->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="mb-3">
                    <label for="judul_karya" class="form-label fw-semibold">
                        Judul Karya <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="judul_karya"
                        name="judul_karya"
                        class="form-control @error('judul_karya') is-invalid @enderror"
                        value="{{ old('judul_karya', $registration->submission?->judul_karya) }}"
                        placeholder="Contoh: SmartTraffic AI untuk Pengurai Kemacetan Kota"
                        required
                    >
                    @error('judul_karya')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi_karya" class="form-label fw-semibold">
                        Deskripsi / Ringkasan Karya <span class="text-danger">*</span>
                    </label>
                    <textarea
                        id="deskripsi_karya"
                        name="deskripsi_karya"
                        rows="4"
                        class="form-control @error('deskripsi_karya') is-invalid @enderror"
                        placeholder="Jelaskan solusi, arsitektur, atau inovasi utama karya Anda (minimal 20 karakter)..."
                        required
                    >{{ old('deskripsi_karya', $registration->submission?->deskripsi_karya) }}</textarea>
                    @error('deskripsi_karya')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="link_karya" class="form-label fw-semibold">
                            Tautan Demo / Repositori <span class="text-body-secondary fw-normal">(Opsional)</span>
                        </label>
                        <input
                            type="url"
                            id="link_karya"
                            name="link_karya"
                            class="form-control @error('link_karya') is-invalid @enderror"
                            value="{{ old('link_karya', $registration->submission?->link_karya) }}"
                            placeholder="https://github.com/tim-kami/karya-olimfy"
                        >
                        @error('link_karya')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="file_karya" class="form-label fw-semibold">
                            Unggah Berkas Karya <span class="text-body-secondary fw-normal">(Opsional, PDF / ZIP maks. 10 MB)</span>
                        </label>
                        <input
                            type="file"
                            id="file_karya"
                            name="file_karya"
                            class="form-control @error('file_karya') is-invalid @enderror"
                            accept=".pdf,.zip"
                        >
                        @if ($registration->submission?->file_karya)
                            <div class="form-text">
                                Berkas saat ini:
                                <a href="{{ asset('storage/' . $registration->submission->file_karya) }}" target="_blank" rel="noopener">
                                    Lihat Berkas Terunggah
                                </a>
                            </div>
                        @endif
                        @error('file_karya')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2 bg-body-tertiary">
                <a href="{{ route('peserta.submission.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-cloud-check me-1" aria-hidden="true"></i>
                    Simpan &amp; Kirim Karya
                </button>
            </div>
        </form>
    </div>
@endsection
