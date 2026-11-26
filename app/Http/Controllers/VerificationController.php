<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VerificationController extends Controller
{
    /**
     * Tampilkan antrean pendaftaran tim pada lomba milik penyelenggara yang login.
     */
    public function index(): View
    {
        $registrations = Registration::with(['competition', 'members', 'documents'])
            ->whereHas('competition', function ($query) {
                $query->where('organizer_id', auth()->id());
            })
            ->latest()
            ->get();

        return view('penyelenggara.validasi', compact('registrations'));
    }

    /**
     * Perbarui status verifikasi dan catatan kurasi sebuah pendaftaran.
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status_verifikasi' => ['required', Rule::in(['diterima', 'ditolak'])],
            'catatan_kurasi' => ['nullable', 'string', 'max:1000'],
        ], [
            'status_verifikasi.required' => 'Status verifikasi wajib dipilih.',
            'status_verifikasi.in' => 'Status verifikasi harus diterima atau ditolak.',
            'catatan_kurasi.string' => 'Catatan kurasi harus berupa teks.',
            'catatan_kurasi.max' => 'Catatan kurasi maksimal 1000 karakter.',
        ]);

        $registration = Registration::with('competition')
            ->whereHas('competition', function ($query) {
                $query->where('organizer_id', auth()->id());
            })
            ->findOrFail($id);

        DB::transaction(function () use ($registration, $validated): void {
            $statusLama = $registration->status_verifikasi;
            $statusBaru = $validated['status_verifikasi'];

            $registration->update([
                'status_verifikasi' => $statusBaru,
                'catatan_kurasi' => $validated['catatan_kurasi'] ?? null,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            if ($statusLama !== 'ditolak' && $statusBaru === 'ditolak') {
                $registration->competition()
                    ->where('kuota_terisi', '>', 0)
                    ->decrement('kuota_terisi');
            }

            $this->notifyHasilVerifikasi($registration, $statusBaru, $validated['catatan_kurasi'] ?? null);
        });

        return redirect()
            ->route('penyelenggara.validasi.index')
            ->with('success', 'Status verifikasi berhasil diperbarui!');
    }

    /**
     * Kirim notifikasi hasil verifikasi kepada pemilik pendaftaran.
     */
    private function notifyHasilVerifikasi(Registration $registration, string $status, ?string $catatan): void
    {
        $judulKompetisi = $registration->competition?->judul ?? 'kompetisi';
        $namaTim = $registration->nama_tim;

        $judul = $status === 'diterima'
            ? 'Pendaftaran Tim Diterima'
            : 'Pendaftaran Tim Ditolak';

        $pesan = $status === 'diterima'
            ? "Selamat! Berkas pendaftaran tim \"{$namaTim}\" pada kompetisi \"{$judulKompetisi}\" telah diterima."
            : "Mohon maaf, berkas pendaftaran tim \"{$namaTim}\" pada kompetisi \"{$judulKompetisi}\" ditolak.";

        if (filled($catatan)) {
            $pesan .= " Catatan kurasi: {$catatan}";
        }

        Notification::create([
            'user_id' => $registration->peserta_id,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => 'verifikasi',
            'is_read' => false,
        ]);
    }
}
