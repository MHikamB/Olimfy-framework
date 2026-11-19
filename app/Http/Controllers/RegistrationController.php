<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Competition;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\RegistrationDocument;
use App\Models\RegistrationMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * Tampilkan daftar riwayat pendaftaran lomba milik peserta yang login.
     */
    public function index(): View
    {
        $registrations = Registration::with(['competition', 'payment', 'members', 'documents'])
            ->where('peserta_id', Auth::id())
            ->latest()
            ->get();

        return view('peserta.pendaftaran', compact('registrations'));
    }

    /**
     * Tampilkan formulir pendaftaran tim untuk kompetisi tertentu.
     */
    public function create(int $competition_id): View|RedirectResponse
    {
        $competition = Competition::with('organizer')->findOrFail($competition_id);

        if ($this->isCompetitionUnavailable($competition)) {
            return redirect()
                ->route('peserta.pendaftaran.index')
                ->with('error', 'Pendaftaran untuk kompetisi ini sudah ditutup, melewati batas waktu, atau kuota penuh.');
        }

        return view('peserta.daftar', compact('competition'));
    }

    /**
     * Simpan pendaftaran tim, anggota, dokumen persyaratan, dan tagihan pembayaran awal.
     */
    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $competition = Competition::findOrFail($validated['competition_id']);

        if ($this->isCompetitionUnavailable($competition)) {
            return back()
                ->withInput()
                ->with('error', 'Pendaftaran tidak dapat diproses karena lomba sudah ditutup atau kuota penuh.');
        }

        $payment = DB::transaction(function () use ($request, $validated, $competition): Payment {
            $registration = Registration::create([
                'competition_id' => $competition->id,
                'peserta_id' => Auth::id(),
                'nama_tim' => $validated['nama_tim'],
                'status_verifikasi' => 'menunggu_verifikasi',
            ]);

            $this->storeTeamMembers($registration, $validated);
            $this->storeRegistrationDocuments($registration, $request);

            $isFree = $competition->biaya_pendaftaran <= 0;

            $payment = Payment::create([
                'registration_id' => $registration->id,
                'paid_by' => $isFree ? Auth::id() : null,
                'kode_invoice' => sprintf('INV-OLIMFY-%s-%04d', now()->format('Ymd'), $registration->id),
                'nominal' => $competition->biaya_pendaftaran,
                'metode_pembayaran' => $isFree ? 'Gratis' : 'QRIS / Virtual Account',
                'status_pembayaran' => $isFree ? 'lunas' : 'menunggu_pembayaran',
                'waktu_bayar' => $isFree ? now() : null,
            ]);

            $competition->increment('kuota_terisi');

            return $payment;
        });

        $message = $payment->status_pembayaran === 'lunas'
            ? 'Pendaftaran tim berhasil dikirim dan otomatis lunas (kompetisi gratis).'
            : 'Pendaftaran tim berhasil dikirim! Silakan selesaikan pembayaran pada invoice di bawah.';

        return redirect()
            ->route('peserta.invoice.show', $payment->id)
            ->with('success', $message);
    }

    /**
     * Periksa apakah kompetisi sedang tutup, melewati deadline, atau kuota penuh.
     */
    private function isCompetitionUnavailable(Competition $competition): bool
    {
        if ($competition->status !== 'Aktif' || $competition->kuota_terisi >= $competition->kuota) {
            return true;
        }

        return $competition->deadline_pendaftaran?->endOfDay()->isPast() ?? false;
    }

    /**
     * Simpan data ketua dan anggota tim (opsional) ke tabel registration_members.
     *
     * @param  array<string, mixed>  $validated
     */
    private function storeTeamMembers(Registration $registration, array $validated): void
    {
        $memberSlots = [
            'ketua' => 'ketua',
            'anggota_1' => 'anggota_1',
            'anggota_2' => 'anggota_2',
        ];

        foreach ($memberSlots as $role => $suffix) {
            $name = $validated["nama_{$suffix}"] ?? null;

            if (blank($name)) {
                continue;
            }

            RegistrationMember::create([
                'registration_id' => $registration->id,
                'nama_lengkap' => $name,
                'peran' => $role,
                'email' => $validated["email_{$suffix}"],
                'no_hp' => $validated["no_hp_{$suffix}"],
            ]);
        }
    }

    /**
     * Simpan file unggahan dokumen KTM dan proposal ke disk public.
     */
    private function storeRegistrationDocuments(Registration $registration, StoreRegistrationRequest $request): void
    {
        $documentTypes = ['ktm_ketua', 'ktm_anggota', 'proposal'];

        foreach ($documentTypes as $docType) {
            if (! $request->hasFile($docType)) {
                continue;
            }

            $filePath = $request->file($docType)->store('uploads/dokumen', 'public');

            RegistrationDocument::create([
                'registration_id' => $registration->id,
                'jenis_dokumen' => $docType,
                'file_path' => $filePath,
            ]);
        }
    }
}
