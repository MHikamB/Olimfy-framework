<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = User::findOrFail(Auth::id());
        $profile = $user->pesertaProfile;

        return view('peserta.profil', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nim_nisn' => 'nullable|string|max:50',
            'institusi' => 'required|string|max:255',
            'jenjang' => 'required|in:SMA/SMK,Mahasiswa,Umum',
            'jurusan' => 'nullable|string|max:255',
            'no_hp' => 'required|string|max:20',
        ]);

        $user = User::findOrFail(Auth::id());
        $user->update(['name' => $request->name]);

        $user->pesertaProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'nim_nisn' => $request->nim_nisn,
                'institusi' => $request->institusi,
                'jenjang' => $request->jenjang,
                'jurusan' => $request->jurusan,
                'no_hp' => $request->no_hp,
            ]
        );

        return back()->with('success', 'Profil akademik berhasil diperbarui!');
    }
}
