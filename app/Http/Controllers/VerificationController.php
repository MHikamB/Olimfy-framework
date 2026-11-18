<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $registration = Registration::whereHas('competition', function ($query) {
            $query->where('organizer_id', auth()->id());
        })->findOrFail($id);

        $registration->update([
            'status_verifikasi' => $validated['status_verifikasi'],
            'catatan_kurasi' => $validated['catatan_kurasi'] ?? null,
        ]);

        return redirect()
            ->route('penyelenggara.validasi.index')
            ->with('success', 'Status verifikasi berhasil diperbarui!');
    }
}
