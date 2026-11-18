<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $popularCompetitions = Competition::with('organizer')
            ->where('status', 'Aktif')
            ->orderByDesc('kuota_terisi')
            ->take(4)
            ->get();

        $totalActiveCompetitions = Competition::where('status', 'Aktif')->count();
        $totalParticipants = DB::table('registrations')->count(); // Sesuaikan jika nama tabel pendaftaran berbeda
        $categories = ['Teknologi', 'Desain', 'Bisnis', 'Sains'];

        return view('home', compact(
            'popularCompetitions',
            'totalActiveCompetitions',
            'totalParticipants',
            'categories'
        ));
    }
}
