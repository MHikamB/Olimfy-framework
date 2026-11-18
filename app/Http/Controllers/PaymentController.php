<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Tampilkan rincian invoice pembayaran untuk pendaftaran peserta.
     */
    public function show(int $id): View
    {
        $payment = Payment::with([
            'registration.competition.organizer',
            'registration.members',
            'registration.documents',
        ])->findOrFail($id);

        abort_unless($payment->registration->peserta_id === Auth::id(), 403);

        return view('peserta.invoice', compact('payment'));
    }

    /**
     * Proses konfirmasi simulasi pembayaran menjadi lunas.
     */
    public function simulasiBayar(Request $request, int $id): RedirectResponse
    {
        $payment = Payment::with('registration')->findOrFail($id);

        abort_unless($payment->registration->peserta_id === Auth::id(), 403);

        if ($payment->status_pembayaran === 'lunas') {
            return redirect()
                ->route('peserta.invoice.show', $payment->id)
                ->with('success', 'Tagihan ini sudah berstatus Lunas.');
        }

        $validated = $request->validate([
            'metode_pembayaran' => ['nullable', 'string', 'in:QRIS,Virtual Account BCA,Virtual Account Mandiri,QRIS / Virtual Account'],
        ]);

        $payment->update([
            'metode_pembayaran' => $validated['metode_pembayaran'] ?? $payment->metode_pembayaran,
            'status_pembayaran' => 'lunas',
            'waktu_bayar' => now(),
        ]);

        return redirect()
            ->route('peserta.invoice.show', $payment->id)
            ->with('success', 'Pembayaran berhasil dikonfirmasi!');
    }
}
