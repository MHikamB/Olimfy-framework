<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompetitionRequest;
use App\Models\Competition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    /**
     * Tampilkan daftar lomba milik penyelenggara yang login.
     */
    public function index(): View
    {
        $competitions = Competition::where('organizer_id', auth()->id())
            ->latest()
            ->get();

        return view('penyelenggara.competitions.index', compact('competitions'));
    }

    /**
     * Tampilkan formulir tambah lomba.
     */
    public function create(): View
    {
        return view('penyelenggara.lomba-buat');
    }

    /**
     * Simpan lomba baru beserta posternya.
     */
    public function store(StoreCompetitionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['poster']);

        $data['organizer_id'] = auth()->id();

        if ($request->hasFile('poster')) {
            $data['poster_path'] = $this->storePoster($request->file('poster'));
        }

        Competition::create($data);

        return redirect()
            ->route('penyelenggara.lomba.index')
            ->with('success', 'Lomba berhasil dipublikasikan!');
    }

    /**
     * Tampilkan formulir edit lomba milik penyelenggara yang login.
     */
    public function edit(int $id): View
    {
        $competition = $this->findOwnedCompetition($id);

        return view('penyelenggara.competitions.edit', compact('competition'));
    }

    /**
     * Perbarui data lomba milik penyelenggara yang login.
     */
    public function update(StoreCompetitionRequest $request, int $id): RedirectResponse
    {
        $competition = $this->findOwnedCompetition($id);

        $data = $request->validated();
        unset($data['poster']);

        if ($request->hasFile('poster')) {
            $this->deletePoster($competition->poster_path);
            $data['poster_path'] = $this->storePoster($request->file('poster'));
        }

        $competition->update($data);

        return redirect()
            ->route('penyelenggara.lomba.index')
            ->with('success', 'Data lomba berhasil diperbarui!');
    }

    /**
     * Hapus lomba beserta posternya.
     */
    public function destroy(int $id): RedirectResponse
    {
        $competition = $this->findOwnedCompetition($id);

        $this->deletePoster($competition->poster_path);
        $competition->delete();

        return redirect()
            ->route('penyelenggara.lomba.index')
            ->with('success', 'Lomba berhasil dihapus!');
    }

    /**
     * Ambil lomba yang benar-benar milik penyelenggara yang login.
     */
    private function findOwnedCompetition(int $id): Competition
    {
        return Competition::where('organizer_id', auth()->id())->findOrFail($id);
    }

    /**
     * Simpan poster ke public/uploads/posters dan kembalikan path relatifnya.
     */
    private function storePoster(UploadedFile $poster): string
    {
        $directory = public_path('uploads/posters');
        File::ensureDirectoryExists($directory);

        $filename = $poster->hashName();
        $poster->move($directory, $filename);

        return 'uploads/posters/'.$filename;
    }

    /**
     * Hapus file poster lama jika masih ada.
     */
    private function deletePoster(?string $posterPath): void
    {
        if ($posterPath && File::exists(public_path($posterPath))) {
            File::delete(public_path($posterPath));
        }
    }
}
