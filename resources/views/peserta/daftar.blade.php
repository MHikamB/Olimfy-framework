@extends('layouts.app')

@section('title', 'Formulir Pendaftaran Tim')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Formulir Pendaftaran Tim</h1>
            <p class="text-body-secondary mb-0">Lengkapi data tim, anggota, dan berkas persyaratan kompetisi.</p>
        </div>
        <a href="{{ route('peserta.pendaftaran.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
            Pendaftaran Saya
        </a>
    </div>

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

    <form method="POST" action="{{ route('peserta.daftar.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="competition_id" value="{{ $competition->id }}">

        {{-- Card 1: Informasi Kompetisi --}}
        <div class="card card-primary card-outline mb-4 shadow-sm">
            <div class="card-header">
                <h2 class="card-title h6 mb-0 fw-bold">
                    <i class="bi bi-trophy me-2" aria-hidden="true"></i>
                    1. Informasi Kompetisi
                </h2>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-body-secondary small">Nama Kompetisi</div>
                        <div class="fw-semibold fs-6">{{ $competition->judul }}</div>
                        <div class="mt-1">
                            <span class="badge bg-primary">{{ $competition->kategori }}</span>
                            <span class="badge bg-info text-dark">{{ $competition->jenjang }}</span>
                            <span class="badge bg-secondary">{{ $competition->mode_pelaksanaan }}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-body-secondary small">Biaya Registrasi</div>
                        <div class="fw-bold text-primary fs-5">{{ formatRupiah($competition->biaya_pendaftaran) }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-body-secondary small">Batas Pendaftaran</div>
                        <div class="fw-semibold">{{ formatTanggalIndo($competition->deadline_pendaftaran) }}</div>
                        <div class="text-body-secondary small mt-1">
                            Sisa Kuota: {{ max(0, $competition->kuota - $competition->kuota_terisi) }} / {{ $competition->kuota }} tim
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Identitas Tim & Ketua --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h2 class="card-title h6 mb-0 fw-bold">
                    <i class="bi bi-people-fill me-2" aria-hidden="true"></i>
                    2. Identitas Tim &amp; Ketua
                </h2>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nama_tim" class="form-label fw-semibold">
                            Nama Tim <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            id="nama_tim"
                            name="nama_tim"
                            class="form-control @error('nama_tim') is-invalid @enderror"
                            value="{{ old('nama_tim') }}"
                            placeholder="Contoh: Tim TechnoNova"
                            required
                        >
                        @error('nama_tim')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="nama_ketua" class="form-label fw-semibold">
                            Nama Lengkap Ketua <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            id="nama_ketua"
                            name="nama_ketua"
                            class="form-control @error('nama_ketua') is-invalid @enderror"
                            value="{{ old('nama_ketua', Auth::user()?->name) }}"
                            placeholder="Masukkan nama lengkap ketua tim"
                            required
                        >
                        @error('nama_ketua')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email_ketua" class="form-label fw-semibold">
                            Email Ketua <span class="text-danger">*</span>
                        </label>
                        <input
                            type="email"
                            id="email_ketua"
                            name="email_ketua"
                            class="form-control @error('email_ketua') is-invalid @enderror"
                            value="{{ old('email_ketua', Auth::user()?->email) }}"
                            placeholder="ketua@example.com"
                            required
                        >
                        @error('email_ketua')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="no_hp_ketua" class="form-label fw-semibold">
                            Nomor WhatsApp / HP Ketua <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            id="no_hp_ketua"
                            name="no_hp_ketua"
                            class="form-control @error('no_hp_ketua') is-invalid @enderror"
                            value="{{ old('no_hp_ketua') }}"
                            placeholder="Contoh: 081234567890"
                            required
                        >
                        @error('no_hp_ketua')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Identitas Anggota Tim (Opsional) --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h2 class="card-title h6 mb-0 fw-bold">
                    <i class="bi bi-person-plus-fill me-2" aria-hidden="true"></i>
                    3. Identitas Anggota Tim <span class="text-body-secondary fw-normal">(Opsional)</span>
                </h2>
            </div>
            <div class="card-body">
                <p class="text-body-secondary small mb-3">
                    Kosongkan bagian ini jika mendaftar sebagai individu, atau lengkapi data jika memiliki anggota tim.
                </p>

                <h3 class="h6 fw-semibold mb-2">Anggota 1</h3>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="nama_anggota_1" class="form-label">Nama Lengkap</label>
                        <input
                            type="text"
                            id="nama_anggota_1"
                            name="nama_anggota_1"
                            class="form-control @error('nama_anggota_1') is-invalid @enderror"
                            value="{{ old('nama_anggota_1') }}"
                            placeholder="Nama anggota 1"
                        >
                        @error('nama_anggota_1')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="email_anggota_1" class="form-label">Email</label>
                        <input
                            type="email"
                            id="email_anggota_1"
                            name="email_anggota_1"
                            class="form-control @error('email_anggota_1') is-invalid @enderror"
                            value="{{ old('email_anggota_1') }}"
                            placeholder="anggota1@example.com"
                        >
                        @error('email_anggota_1')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="no_hp_anggota_1" class="form-label">Nomor WhatsApp / HP</label>
                        <input
                            type="text"
                            id="no_hp_anggota_1"
                            name="no_hp_anggota_1"
                            class="form-control @error('no_hp_anggota_1') is-invalid @enderror"
                            value="{{ old('no_hp_anggota_1') }}"
                            placeholder="081234567891"
                        >
                        @error('no_hp_anggota_1')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h3 class="h6 fw-semibold mb-2">Anggota 2</h3>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="nama_anggota_2" class="form-label">Nama Lengkap</label>
                        <input
                            type="text"
                            id="nama_anggota_2"
                            name="nama_anggota_2"
                            class="form-control @error('nama_anggota_2') is-invalid @enderror"
                            value="{{ old('nama_anggota_2') }}"
                            placeholder="Nama anggota 2"
                        >
                        @error('nama_anggota_2')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="email_anggota_2" class="form-label">Email</label>
                        <input
                            type="email"
                            id="email_anggota_2"
                            name="email_anggota_2"
                            class="form-control @error('email_anggota_2') is-invalid @enderror"
                            value="{{ old('email_anggota_2') }}"
                            placeholder="anggota2@example.com"
                        >
                        @error('email_anggota_2')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="no_hp_anggota_2" class="form-label">Nomor WhatsApp / HP</label>
                        <input
                            type="text"
                            id="no_hp_anggota_2"
                            name="no_hp_anggota_2"
                            class="form-control @error('no_hp_anggota_2') is-invalid @enderror"
                            value="{{ old('no_hp_anggota_2') }}"
                            placeholder="081234567892"
                        >
                        @error('no_hp_anggota_2')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Unggah Dokumen Berkas --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h2 class="card-title h6 mb-0 fw-bold">
                    <i class="bi bi-file-earmark-arrow-up-fill me-2" aria-hidden="true"></i>
                    4. Unggah Dokumen Berkas
                </h2>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="ktm_ketua" class="form-label fw-semibold">
                            KTM / Kartu Pelajar Ketua <span class="text-danger">*</span>
                        </label>
                        <input
                            type="file"
                            id="ktm_ketua"
                            name="ktm_ketua"
                            class="form-control @error('ktm_ketua') is-invalid @enderror"
                            accept=".pdf,.jpg,.jpeg,.png"
                            required
                        >
                        <div class="form-text">Format: PDF, JPG, JPEG, atau PNG (Maks. 2 MB).</div>
                        @error('ktm_ketua')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="ktm_anggota" class="form-label fw-semibold">
                            KTM Anggota <span class="text-body-secondary fw-normal">(Opsional)</span>
                        </label>
                        <input
                            type="file"
                            id="ktm_anggota"
                            name="ktm_anggota"
                            class="form-control @error('ktm_anggota') is-invalid @enderror"
                            accept=".pdf,.jpg,.jpeg,.png"
                        >
                        <div class="form-text">Gabungkan bila lebih dari 1 anggota (Maks. 2 MB).</div>
                        @error('ktm_anggota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="proposal" class="form-label fw-semibold">
                            Proposal / Abstrak Karya <span class="text-body-secondary fw-normal">(Opsional)</span>
                        </label>
                        <input
                            type="file"
                            id="proposal"
                            name="proposal"
                            class="form-control @error('proposal') is-invalid @enderror"
                            accept=".pdf"
                        >
                        <div class="form-text">Format: PDF (Maks. 5 MB).</div>
                        @error('proposal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2 bg-body-tertiary">
                <a href="{{ url('/peserta/dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send-check me-1" aria-hidden="true"></i>
                    Kirim Pendaftaran &amp; Lanjut ke Pembayaran
                </button>
            </div>
        </div>
    </form>
@endsection
