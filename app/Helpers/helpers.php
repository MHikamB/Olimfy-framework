<?php

use Carbon\Carbon;

if (! function_exists('formatRupiah')) {
    /**
     * Format angka numerik menjadi string mata uang Rupiah.
     */
    function formatRupiah(int|float|string|null $angka): string
    {
        $nilai = (int) ($angka ?? 0);

        if ($nilai <= 0) {
            return 'Gratis';
        }

        return 'Rp '.number_format($nilai, 0, ',', '.');
    }
}

if (! function_exists('formatTanggalIndo')) {
    /**
     * Format tanggal menjadi format bahasa Indonesia yang ramah pengguna.
     */
    function formatTanggalIndo(DateTimeInterface|string|null $tanggal): string
    {
        if (empty($tanggal)) {
            return '-';
        }

        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $date = $tanggal instanceof DateTimeInterface
            ? Carbon::instance($tanggal)
            : Carbon::parse($tanggal);

        return $date->day.' '.$bulan[$date->month].' '.$date->year;
    }
}

if (! function_exists('statusBadge')) {
    /**
     * Hasilkan HTML badge Bootstrap 5 / AdminLTE 4 sesuai status pendaftaran atau pembayaran.
     */
    function statusBadge(?string $status): string
    {
        return match ($status) {
            'menunggu_verifikasi' => '<span class="badge bg-warning text-dark">Menunggu Verifikasi</span>',
            'diterima' => '<span class="badge bg-success">Lolos Seleksi</span>',
            'ditolak' => '<span class="badge bg-danger">Berkas Ditolak</span>',
            'dibatalkan' => '<span class="badge bg-dark">Dibatalkan</span>',
            'lunas' => '<span class="badge bg-success">Lunas</span>',
            'menunggu_pembayaran' => '<span class="badge bg-secondary">Belum Bayar</span>',
            'dikirim' => '<span class="badge bg-info text-dark">Karya Dikirim</span>',
            'dinilai' => '<span class="badge bg-primary">Sudah Dinilai</span>',
            'finalis' => '<span class="badge bg-warning text-dark">Finalis</span>',
            default => '<span class="badge bg-light text-dark">'.e((string) $status).'</span>',
        };
    }
}
