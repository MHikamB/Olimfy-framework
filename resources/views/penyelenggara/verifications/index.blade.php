@extends('layouts.app')

@section('title', 'Validasi Berkas')

@section('content')
    <div class="mb-3">
        <h1 class="h4 mb-1">Validasi Berkas</h1>
        <p class="text-body-secondary mb-0">
            Tinjau anggota tim dan dokumen pendaftaran pada lomba yang Anda selenggarakan.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-3" role="alert">
            <h2 class="h6 fw-bold mb-2">
                <i class="bi bi-exclamation-circle-fill me-1" aria-hidden="true"></i>
                Periksa kembali data verifikasi:
            </h2>
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
                <i class="bi bi-file-earmark-check me-2" aria-hidden="true"></i>
                Antrean Pendaftaran Tim
            </h2>
        </div>

        <div class="card-body p-0">
            @if ($registrations->isEmpty())
                <div class="p-5 text-center text-body-secondary">
                    <i class="bi bi-inbox fs-1 d-block mb-2" aria-hidden="true"></i>
                    <p class="mb-0">Belum ada tim yang mendaftar pada lomba Anda.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Tim</th>
                                <th>Lomba</th>
                                <th>Anggota</th>
                                <th>Berkas</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registrations as $registration)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold">{{ $registration->nama_tim }}</div>
                                        <div class="small text-body-secondary">
                                            {{ $registration->created_at?->format('d/m/Y H:i') ?? '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $registration->competition?->judul ?? '-' }}
                                        </div>
                                        <div class="small text-body-secondary">
                                            {{ $registration->competition?->kategori }}
                                        </div>
                                    </td>
                                    <td>
                                        @forelse ($registration->members as $member)
                                            <div class="small">
                                                <span class="fw-semibold">{{ $member->nama_lengkap }}</span>
                                                <span class="text-body-secondary">
                                                    ({{ str_replace('_', ' ', $member->peran) }})
                                                </span>
                                            </div>
                                        @empty
                                            <span class="text-body-secondary small">-</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @forelse ($registration->documents as $document)
                                            <a href="{{ asset('storage/'.$document->file_path) }}"
                                               target="_blank"
                                               rel="noopener"
                                               class="badge bg-light text-dark border text-decoration-none me-1 mb-1">
                                                <i class="bi bi-file-earmark-text me-1" aria-hidden="true"></i>
                                                {{ strtoupper(str_replace('_', ' ', $document->jenis_dokumen)) }}
                                            </a>
                                        @empty
                                            <span class="text-body-secondary small">Tidak ada berkas</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        {!! statusBadge($registration->status_verifikasi) !!}
                                        @if ($registration->catatan_kurasi)
                                            <div class="small text-body-secondary mt-1">
                                                Catatan: {{ $registration->catatan_kurasi }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#verifyModal{{ $registration->id }}">
                                            <i class="bi bi-check2-square me-1" aria-hidden="true"></i>
                                            Verifikasi
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @foreach ($registrations as $registration)
        <div class="modal fade"
             id="verifyModal{{ $registration->id }}"
             tabindex="-1"
             aria-labelledby="verifyModalLabel{{ $registration->id }}"
             aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('penyelenggara.validasi.update', $registration->id) }}">
                    @csrf
                    @method('PATCH')

                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title h5" id="verifyModalLabel{{ $registration->id }}">
                                Verifikasi {{ $registration->nama_tim }}
                            </h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="status_verifikasi_{{ $registration->id }}" class="form-label fw-semibold">
                                    Status Verifikasi <span class="text-danger">*</span>
                                </label>
                                <select id="status_verifikasi_{{ $registration->id }}"
                                        name="status_verifikasi"
                                        class="form-select"
                                        required>
                                    <option value="" disabled @selected($registration->status_verifikasi === 'menunggu_verifikasi')>
                                        Pilih status...
                                    </option>
                                    <option value="diterima" @selected($registration->status_verifikasi === 'diterima')>
                                        Diterima
                                    </option>
                                    <option value="ditolak" @selected($registration->status_verifikasi === 'ditolak')>
                                        Ditolak
                                    </option>
                                </select>
                            </div>

                            <div class="mb-0">
                                <label for="catatan_kurasi_{{ $registration->id }}" class="form-label fw-semibold">
                                    Catatan Kurasi
                                </label>
                                <textarea id="catatan_kurasi_{{ $registration->id }}"
                                          name="catatan_kurasi"
                                          rows="3"
                                          class="form-control"
                                          placeholder="Contoh: Berkas KTM tidak terbaca atau proposal belum lengkap.">{{ old('catatan_kurasi', $registration->catatan_kurasi) }}</textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1" aria-hidden="true"></i>
                                Simpan Status
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
