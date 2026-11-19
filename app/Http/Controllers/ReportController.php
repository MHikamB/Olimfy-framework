<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Tampilkan laporan rekapitulasi kompetisi dan kategori menggunakan Query Builder.
     */
    public function index(Request $request): View
    {
        $organizerId = Auth::id();
        $selectedKategori = $request->input('kategori');
        $selectedStatus = $request->input('status');

        $competitionQuery = DB::table('competitions')
            ->join('users', 'competitions.organizer_id', '=', 'users.id')
            ->leftJoin('registrations', 'competitions.id', '=', 'registrations.competition_id')
            ->leftJoin('payments', 'registrations.id', '=', 'payments.registration_id')
            ->leftJoin('submissions', 'registrations.id', '=', 'submissions.registration_id')
            ->where('competitions.organizer_id', $organizerId);

        if (! empty($selectedKategori)) {
            $competitionQuery->where('competitions.kategori', $selectedKategori);
        }

        if (! empty($selectedStatus)) {
            $competitionQuery->where('competitions.status', $selectedStatus);
        }

        $competitionReports = $competitionQuery
            ->select(
                'competitions.id',
                'competitions.judul',
                'competitions.kategori',
                'competitions.jenjang',
                'competitions.status',
                'competitions.kuota',
                'competitions.kuota_terisi',
                'users.name as nama_penyelenggara',
            )
            ->selectRaw('COUNT(DISTINCT registrations.id) as total_pendaftar')
            ->selectRaw("COUNT(DISTINCT CASE WHEN registrations.status_verifikasi = 'diterima' THEN registrations.id END) as pendaftar_lolos")
            ->selectRaw("COUNT(DISTINCT CASE WHEN registrations.status_verifikasi = 'ditolak' THEN registrations.id END) as pendaftar_ditolak")
            ->selectRaw("COALESCE(SUM(CASE WHEN payments.status_pembayaran = 'lunas' THEN payments.nominal ELSE 0 END), 0) as total_pendapatan")
            ->selectRaw('AVG(submissions.nilai) as rata_rata_nilai')
            ->groupBy(
                'competitions.id',
                'competitions.judul',
                'competitions.kategori',
                'competitions.jenjang',
                'competitions.status',
                'competitions.kuota',
                'competitions.kuota_terisi',
                'users.name',
            )
            ->orderByDesc('total_pendaftar')
            ->get();

        $categoryReports = DB::table('competitions')
            ->leftJoin('registrations', 'competitions.id', '=', 'registrations.competition_id')
            ->leftJoin('payments', 'registrations.id', '=', 'payments.registration_id')
            ->where('competitions.organizer_id', $organizerId)
            ->select('competitions.kategori')
            ->selectRaw('COUNT(DISTINCT competitions.id) as jumlah_lomba')
            ->selectRaw('COUNT(DISTINCT registrations.id) as total_pendaftar')
            ->selectRaw("COALESCE(SUM(CASE WHEN payments.status_pembayaran = 'lunas' THEN payments.nominal ELSE 0 END), 0) as total_pendapatan")
            ->groupBy('competitions.kategori')
            ->orderByDesc('total_pendapatan')
            ->get();

        $summary = [
            'total_lomba' => $competitionReports->count(),
            'total_pendaftar' => (int) $competitionReports->sum('total_pendaftar'),
            'total_lolos' => (int) $competitionReports->sum('pendaftar_lolos'),
            'total_pendapatan' => (int) $competitionReports->sum('total_pendapatan'),
        ];

        $kategoriList = DB::table('competitions')
            ->where('organizer_id', $organizerId)
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        return view('penyelenggara.laporan', compact(
            'competitionReports',
            'categoryReports',
            'summary',
            'kategoriList',
            'selectedKategori',
            'selectedStatus',
        ));
    }
}
