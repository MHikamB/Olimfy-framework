<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $query = Competition::with('organizer')->where('status', 'Aktif');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->whereIn('kategori', (array) $request->kategori);
        }

        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        if ($request->filled('mode')) {
            $query->where('mode_pelaksanaan', $request->mode);
        }

        switch ($request->sort) {
            case 'deadline':
                $query->orderBy('deadline_pendaftaran', 'asc');
                break;
            case 'termurah':
                $query->orderBy('biaya_pendaftaran', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $competitions = $query->paginate(9)->withQueryString();

        return view('explore', compact('competitions'));
    }
}
