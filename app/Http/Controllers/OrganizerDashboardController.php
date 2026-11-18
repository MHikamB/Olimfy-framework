<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class OrganizerDashboardController extends Controller
{
    /**
     * Tampilkan ringkasan statistik dan kompetisi terbaru milik penyelenggara.
     */
    public function index(): View
    {
        $organizerId = auth()->id();

        $competitions = Competition::where('organizer_id', $organizerId)
            ->latest()
            ->take(5)
            ->get();

        $totalLombaAktif = Competition::where('organizer_id', $organizerId)
            ->where('status', 'Aktif')
            ->count();

        $totalTimPendaftar = $this->registrationsForOrganizer($organizerId)->count();

        $menungguVerifikasi = $this->registrationsForOrganizer($organizerId)
            ->where('status_verifikasi', 'menunggu_verifikasi')
            ->count();

        $timLolos = $this->registrationsForOrganizer($organizerId)
            ->where('status_verifikasi', 'diterima')
            ->count();

        return view('penyelenggara.dashboard', compact(
            'competitions',
            'totalLombaAktif',
            'totalTimPendaftar',
            'menungguVerifikasi',
            'timLolos',
        ));
    }

    /**
     * Query dasar pendaftaran pada seluruh lomba milik penyelenggara.
     */
    private function registrationsForOrganizer(int $organizerId): Builder
    {
        return Registration::query()->whereHas('competition', function (Builder $query) use ($organizerId) {
            $query->where('organizer_id', $organizerId);
        });
    }
}
