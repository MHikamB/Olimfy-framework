@extends('layouts.app')

@section('title', 'Invoice ' . $payment->kode_invoice)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">Invoice Tagihan Pendaftaran</h1>
            <p class="text-body-secondary mb-0">Rincian biaya registrasi kompetisi dan simulasi konfirmasi pembayaran.</p>
        </div>
        <a href="{{ route('peserta.pendaftaran.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
            Kembali ke Pendaftaran Saya
        </a>
    </div>

    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <span class="fw-bold fs-5 text-primary">
                    <i class="bi bi-award-fill me-1" aria-hidden="true"></i>
                    OLIMFY
                </span>
                <span class="text-body-secondary ms-2 small">Platform Manajemen Kompetisi</span>
            </div>
            <div class="text-end">
                <div class="fw-bold">{{ $payment->kode_invoice }}</div>
                <div class="small text-body-secondary">Tanggal Tagihan: {{ formatTanggalIndo($payment->created_at) }}</div>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <h2 class="h6 text-body-secondary text-uppercase fw-bold mb-2">Ditagihkan Kepada</h2>
                    <div class="fw-semibold fs-6">{{ $payment->registration->nama_tim }}</div>
                    <div class="small text-body-secondary mt-1">
                        @foreach ($payment->registration->members as $member)
                            <div>
                                {{ ucfirst(str_replace('_', ' ', $member->peran)) }}:
                                <span class="text-body">{{ $member->nama_lengkap }}</span>
                                ({{ $member->email }})
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-6 text-md-end">
                    <h2 class="h6 text-body-secondary text-uppercase fw-bold mb-2">Status &amp; Verifikasi</h2>
                    <div class="mb-2">
                        Status Bayar: {!! statusBadge($payment->status_pembayaran) !!}
                    </div>
                    <div class="mb-2">
                        Status Berkas: {!! statusBadge($payment->registration->status_verifikasi) !!}
                    </div>
                    <div class="small text-body-secondary">
                        Metode: <span class="fw-semibold text-body">{{ $payment->metode_pembayaran }}</span>
                    </div>
                    @if ($payment->waktu_bayar)
                        <div class="small text-success mt-1">
                            Dibayar pada: {{ formatTanggalIndo($payment->waktu_bayar) }} ({{ $payment->waktu_bayar->format('H:i') }} WIB)
                        </div>
                    @endif
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Deskripsi Item</th>
                            <th>Kategori &amp; Jenjang</th>
                            <th class="text-end">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="fw-semibold">
                                    Biaya Pendaftaran: {{ $payment->registration->competition?->judul ?? '-' }}
                                </div>
                                <div class="small text-body-secondary">
                                    Penyelenggara: {{ $payment->registration->competition?->organizer?->name ?? '-' }}
                                </div>
                            </td>
                            <td>
                                {{ $payment->registration->competition?->kategori ?? '-' }}
                                &middot;
                                {{ $payment->registration->competition?->jenjang ?? '-' }}
                            </td>
                            <td class="text-end fw-semibold">{{ formatRupiah($payment->nominal) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="2" class="text-end">Total Tagihan</th>
                            <th class="text-end fs-5 text-primary">{{ formatRupiah($payment->nominal) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($payment->status_pembayaran === 'menunggu_pembayaran')
                <div class="border rounded p-4 bg-body-tertiary">
                    <div class="row align-items-center g-4">
                        <div class="col-md-6">
                            <h3 class="h6 fw-bold mb-2">
                                <i class="bi bi-qr-code-scan me-1" aria-hidden="true"></i>
                                Simulasi Pembayaran QRIS / Transfer Virtual Account
                            </h3>
                            <p class="small text-body-secondary mb-2">
                                Pilih metode pembayaran simulasi di samping, lalu klik tombol konfirmasi untuk menandai tagihan ini sebagai lunas.
                            </p>
                            <div class="p-3 bg-body border rounded small">
                                <div><strong>Nomor VA Simulasi:</strong> 88010{{ str_pad((string) $payment->registration_id, 6, '0', STR_PAD_LEFT) }}</div>
                                <div><strong>Atas Nama:</strong> OLIMFY Escrow Kompetisi</div>
                                <div><strong>Nominal Transfer:</strong> {{ formatRupiah($payment->nominal) }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <form method="POST" action="{{ route('peserta.invoice.bayar', $payment->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="metode_pembayaran" class="form-label fw-semibold">Metode Pembayaran</label>
                                    <select id="metode_pembayaran" name="metode_pembayaran" class="form-select @error('metode_pembayaran') is-invalid @enderror">
                                        <option value="QRIS" {{ old('metode_pembayaran') === 'QRIS' ? 'selected' : '' }}>QRIS Instan</option>
                                        <option value="Virtual Account BCA" {{ old('metode_pembayaran') === 'Virtual Account BCA' ? 'selected' : '' }}>Virtual Account BCA</option>
                                        <option value="Virtual Account Mandiri" {{ old('metode_pembayaran') === 'Virtual Account Mandiri' ? 'selected' : '' }}>Virtual Account Mandiri</option>
                                    </select>
                                    @error('metode_pembayaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check2-circle me-1" aria-hidden="true"></i>
                                    Konfirmasi Pembayaran (Simulasi)
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-success mb-0 d-flex align-items-center" role="alert">
                    <i class="bi bi-patch-check-fill fs-4 me-3" aria-hidden="true"></i>
                    <div>
                        <div class="fw-bold">Pembayaran Terkonfirmasi Lunas</div>
                        <div class="small">
                            Berkas tim Anda siap diperiksa oleh panitia penyelenggara. Pantau hasil verifikasi di menu Pendaftaran Saya.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
