<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::id());
        $bookmarks = $user->bookmarkedCompetitions()->with('organizer')->get();

        return view('peserta.bookmarks', compact('bookmarks'));
    }

    public function toggle(int|string $id)
    {
        $user = User::findOrFail(Auth::id());
        $result = $user->bookmarkedCompetitions()->toggle($id);

        $message = count($result['attached']) > 0
            ? 'Lomba berhasil ditambahkan ke daftar simpanan!'
            : 'Lomba dihapus dari daftar simpanan.';

        return back()->with('success', $message);
    }
}
