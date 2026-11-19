<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $payment = Payment::with('registration.competition')->findOrFail($id);

        abort_unless($payment->registration->peserta_id === Auth::id(), 403);

        if ($payment->status_pembayaran === 'lunas') {
            return redirect()
                ->route('peserta.invoice.show', $payment->id)
                ->with('success', 'Tagihan ini sudah berstatus Lunas.');
        }

        $validated = $request->validate([
            'metode_pembayaran' => ['nullable', 'string', 'in:QRIS,Virtual Account BCA,Virtual Account Mandiri,QRIS / Virtual Account'],
        ]);

        DB::transaction(function () use ($payment, $validated): void {
            $payment->update([
                'paid_by' => Auth::id(),
                'metode_pembayaran' => $validated['metode_pembayaran'] ?? $payment->metode_pembayaran,
                'status_pembayaran' => 'lunas',
                'waktu_bayar' => now(),
            ]);

            Notification::create([
                'user_id' => Auth::id(),
                'judul' => 'Pembayaran Terkonfirmasi Lunas',
                'pesan' => sprintf(
                    'Tagihan %s untuk tim %s (%s) sebesar %s telah lunas.',
                    $payment->kode_invoice,
                    $payment->registration->nama_tim,
                    $payment->registration->competition?->judul ?? 'Kompetisi',
                    formatRupiah($payment->nominal),
                ),
                'tipe' => 'pembayaran',
                'is_read' => false,
            ]);
        });

        return redirect()
            ->route('peserta.invoice.show', $payment->id)
            ->with('success', 'Pembayaran berhasil dikonfirmasi!');
    }
}
