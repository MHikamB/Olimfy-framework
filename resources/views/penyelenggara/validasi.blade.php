@extends('layouts.app')

@section('title', 'Validasi Berkas')

@section('content')
    <div class="mb-3">
        <h1 class="h4 mb-1">Validasi Berkas</h1>
        <p class="text-body-secondary mb-0">
            Tinjau kelengkapan berkas dan tentukan status kelolosan setiap tim.
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
                Daftar Tim Pendaftar
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
                                <th class="ps-3">Nama Tim</th>
                                <th>Kompetisi</th>
                                <th>Ketua Tim</th>
                                <th>Berkas KTM/Proposal</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($registrations as $registration)
                                @php($ketua = $registration->members->firstWhere('peran', 'ketua'))

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
                                        <div class="fw-semibold">{{ $ketua?->nama_lengkap ?? '-' }}</div>
                                        @if ($ketua)
                                            <div class="small text-body-secondary">
                                                {{ $ketua->email }} &middot; {{ $ketua->no_hp }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @forelse ($registration->documents as $document)
                                            <a href="{{ asset('storage/'.$document->file_path) }}"
                                               target="_blank"
                                               rel="noopener"
                                               class="badge bg-light text-dark border text-decoration-none me-1 mb-1">
                                                <i class="bi bi-file-earmark-text me-1" aria-hidden="true"></i>
                                                {{ ucwords(str_replace('_', ' ', $document->jenis_dokumen)) }}
                                            </a>
                                        @empty
                                            <span class="text-body-secondary small">Tidak ada berkas</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        {!! statusBadge($registration->status_verifikasi) !!}
                                        @if ($registration->catatan_kurasi)
                                            <div class="small text-body-secondary mt-1">
                                                {{ $registration->catatan_kurasi }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex justify-content-end gap-1">
                                            <button type="button"
                                                    class="btn btn-sm btn-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalLolos{{ $registration->id }}">
                                                <i class="bi bi-check-lg me-1" aria-hidden="true"></i>
                                                Loloskan
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalTolak{{ $registration->id }}">
                                                <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
                                                Tolak
                                            </button>
                                        </div>
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
        {{-- Modal Loloskan --}}
        <div class="modal fade"
             id="modalLolos{{ $registration->id }}"
             tabindex="-1"
             aria-labelledby="modalLolosLabel{{ $registration->id }}"
             aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('penyelenggara.validasi.update', $registration->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status_verifikasi" value="diterima">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title h5" id="modalLolosLabel{{ $registration->id }}">
                                Loloskan {{ $registration->nama_tim }}
                            </h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>

                        <div class="modal-body">
                            <p class="text-body-secondary">
                                Tim akan ditandai sebagai <strong>Lolos Seleksi</strong>.
                            </p>
                            <label for="catatan_lolos_{{ $registration->id }}" class="form-label fw-semibold">
                                Catatan Kurasi
                            </label>
                            <textarea id="catatan_lolos_{{ $registration->id }}"
                                      name="catatan_kurasi"
                                      rows="3"
                                      class="form-control"
                                      placeholder="Opsional, misalnya: berkas lengkap dan sesuai ketentuan.">{{ $registration->catatan_kurasi }}</textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-1" aria-hidden="true"></i>
                                Loloskan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Tolak --}}
        <div class="modal fade"
             id="modalTolak{{ $registration->id }}"
             tabindex="-1"
             aria-labelledby="modalTolakLabel{{ $registration->id }}"
             aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('penyelenggara.validasi.update', $registration->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status_verifikasi" value="ditolak">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title h5" id="modalTolakLabel{{ $registration->id }}">
                                Tolak {{ $registration->nama_tim }}
                            </h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>

                        <div class="modal-body">
                            <p class="text-body-secondary">
                                Tim akan ditandai sebagai <strong>Berkas Ditolak</strong>.
                            </p>
                            <label for="catatan_tolak_{{ $registration->id }}" class="form-label fw-semibold">
                                Catatan Kurasi <span class="text-danger">*</span>
                            </label>
                            <textarea id="catatan_tolak_{{ $registration->id }}"
                                      name="catatan_kurasi"
                                      rows="3"
                                      class="form-control"
                                      placeholder="Jelaskan alasan penolakan agar tim dapat memperbaiki berkas."
                                      required></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
                                Tolak
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
