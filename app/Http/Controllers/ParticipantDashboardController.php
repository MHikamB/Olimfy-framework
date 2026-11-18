<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParticipantDashboardController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::id());
        $profile = $user->pesertaProfile;

        $totalRegistrations = DB::table('registrations')->where('user_id', $user->id)->count();
        $totalBookmarks = $user->bookmarkedCompetitions()->count();
        $notifications = $user->notifications()->latest()->take(5)->get();

        $jenjang = $profile->jenjang ?? 'Mahasiswa';
        $recommendations = Competition::where('status', 'Aktif')
            ->where('jenjang', $jenjang)
            ->latest()
            ->take(3)
            ->get();

        return view('peserta.dashboard', compact(
            'totalRegistrations',
            'totalBookmarks',
            'notifications',
            'recommendations'
        ));
    }
}
