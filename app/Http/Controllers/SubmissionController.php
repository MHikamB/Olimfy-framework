<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Notification;
use App\Models\Registration;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    /**
     * Tampilkan daftar pendaftaran yang memenuhi syarat pengumpulan karya bagi peserta.
     */
    public function index(): View
    {
        $registrations = Registration::with(['competition', 'payment', 'submission'])
            ->where('peserta_id', Auth::id())
            ->where('status_verifikasi', 'diterima')
            ->whereHas('payment', function ($query) {
                $query->where('status_pembayaran', 'lunas');
            })
            ->latest()
            ->get();

        return view('peserta.submissions', compact('registrations'));
    }

    /**
     * Tampilkan formulir unggah atau perbarui karya lomba untuk tim peserta.
     */
    public function create(int $registration_id): View|RedirectResponse
    {
        $registration = Registration::with(['competition', 'payment', 'submission'])
            ->findOrFail($registration_id);

        abort_unless($registration->peserta_id === Auth::id(), 403);

        if (! $this->isEligibleForSubmission($registration)) {
            return redirect()
                ->route('peserta.submission.index')
                ->with('error', 'Karya hanya dapat dikumpulkan setelah berkas diterima dan pembayaran lunas.');
        }

        if ($this->isAlreadyGraded($registration->submission)) {
            return redirect()
                ->route('peserta.submission.index')
                ->with('error', 'Karya yang sudah dinilai oleh juri tidak dapat diubah kembali.');
        }

        return view('peserta.submission-form', compact('registration'));
    }

    /**
     * Simpan atau perbarui pengumpulan karya peserta.
     */
    public function store(StoreSubmissionRequest $request, int $registration_id): RedirectResponse
    {
        $registration = Registration::with(['competition', 'payment', 'submission'])
            ->findOrFail($registration_id);

        abort_unless($registration->peserta_id === Auth::id(), 403);

        if (! $this->isEligibleForSubmission($registration)) {
            return redirect()
                ->route('peserta.submission.index')
                ->with('error', 'Pendaftaran belum memenuhi syarat (berkas harus diterima dan pembayaran lunas).');
        }

        if ($this->isAlreadyGraded($registration->submission)) {
            return redirect()
                ->route('peserta.submission.index')
                ->with('error', 'Karya yang sudah dinilai oleh juri tidak dapat diubah kembali.');
        }

        $validated = $request->validated();
        $filePath = $registration->submission?->file_karya;

        if ($request->hasFile('file_karya')) {
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file_karya')->store('uploads/karya', 'public');
        }

        Submission::updateOrCreate(
            ['registration_id' => $registration->id],
            [
                'competition_id' => $registration->competition_id,
                'submitted_by' => Auth::id(),
                'judul_karya' => $validated['judul_karya'],
                'deskripsi_karya' => $validated['deskripsi_karya'],
                'link_karya' => $validated['link_karya'] ?? null,
                'file_karya' => $filePath,
                'status' => 'dikirim',
                'submitted_at' => now(),
            ]
        );

        return redirect()
            ->route('peserta.submission.index')
            ->with('success', 'Karya tim berhasil dikumpulkan!');
    }

    /**
     * Tampilkan daftar karya peserta pada kompetisi milik penyelenggara yang login.
     */
    public function organizerIndex(): View
    {
        $submissions = Submission::with(['registration.members', 'competition', 'submitter', 'grader'])
            ->whereHas('competition', function ($query) {
                $query->where('organizer_id', Auth::id());
            })
            ->latest('submitted_at')
            ->get();

        return view('penyelenggara.submissions', compact('submissions'));
    }

    /**
     * Simpan nilai, status penilaian, dan catatan juri untuk karya peserta.
     */
    public function grade(Request $request, int $id): RedirectResponse
    {
        $submission = Submission::with(['competition', 'registration'])
            ->whereHas('competition', function ($query) {
                $query->where('organizer_id', Auth::id());
            })
            ->findOrFail($id);

        $validated = $request->validate([
            'nilai' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => ['required', Rule::in(['dinilai', 'finalis'])],
            'catatan_juri' => ['nullable', 'string', 'max:1000'],
        ], [
            'nilai.required' => 'Nilai karya wajib diisi.',
            'nilai.integer' => 'Nilai karya harus berupa angka bulat.',
            'nilai.min' => 'Nilai minimal adalah 0.',
            'nilai.max' => 'Nilai maksimal adalah 100.',
            'status.required' => 'Status penilaian wajib dipilih.',
            'status.in' => 'Status penilaian harus dinilai atau finalis.',
            'catatan_juri.max' => 'Catatan juri maksimal 1000 karakter.',
        ]);

        DB::transaction(function () use ($submission, $validated): void {
            $submission->update([
                'nilai' => $validated['nilai'],
                'status' => $validated['status'],
                'catatan_juri' => $validated['catatan_juri'] ?? null,
                'graded_by' => Auth::id(),
                'graded_at' => now(),
            ]);

            Notification::create([
                'user_id' => $submission->submitted_by,
                'judul' => 'Hasil Penilaian Karya: '.$submission->judul_karya,
                'pesan' => sprintf(
                    'Karya tim %s pada %s telah dinilai (%s) dengan skor %d/100.',
                    $submission->registration->nama_tim,
                    $submission->competition->judul,
                    strtoupper($validated['status']),
                    $validated['nilai'],
                ),
                'tipe' => 'info',
                'is_read' => false,
            ]);
        });

        return redirect()
            ->route('penyelenggara.submission.index')
            ->with('success', 'Nilai dan status karya berhasil disimpan!');
    }

    /**
     * Pastikan pendaftaran telah lolos verifikasi berkas dan lunas pembayaran.
     */
    private function isEligibleForSubmission(Registration $registration): bool
    {
        return $registration->status_verifikasi === 'diterima'
            && $registration->payment?->status_pembayaran === 'lunas';
    }

    /**
     * Pastikan karya belum dinilai oleh penyelenggara sebelum diubah peserta.
     */
    private function isAlreadyGraded(?Submission $submission): bool
    {
        return $submission !== null && in_array($submission->status, ['dinilai', 'finalis'], true);
    }
}
