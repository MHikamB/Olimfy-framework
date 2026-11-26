<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CompetitionDetailController extends Controller
{
    public function show(int|string $id)
    {
        $competition = Competition::with('organizer')->findOrFail($id);
        $isBookmarked = false;

        if (Auth::check()) {
            $user = User::findOrFail(Auth::id());
            $isBookmarked = $user->bookmarkedCompetitions()->where('competition_id', $id)->exists();
        }

        return view('lomba-detail', compact('competition', 'isBookmarked'));
    }
    
}
