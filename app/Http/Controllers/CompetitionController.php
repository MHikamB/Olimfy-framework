<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompetitionRequest;
use App\Models\Competition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class CompetitionController extends Controller
{

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $competitions = Competition::query()
            ->where('organizer_id', auth()->id())
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('judul', 'like', "%{$search}%")
                        ->orWhere('kategori', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('penyelenggara.competitions.index', compact('competitions', 'search'));
    }

    public function create(): View
    {
        return view('penyelenggara.lomba-buat');
    }

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

    public function edit(int $id): View
    {
        $competition = $this->findOwnedCompetition($id);

        return view('penyelenggara.competitions.edit', compact('competition'));
    }

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

    public function destroy(int $id): RedirectResponse
    {
        $competition = $this->findOwnedCompetition($id);

        if ($competition->registrations()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Kompetisi tidak dapat dihapus karena sudah memiliki data pendaftaran peserta.');
        }

        $this->deletePoster($competition->poster_path);
        $competition->delete();

        return redirect()
            ->route('penyelenggara.lomba.index')
            ->with('success', 'Lomba berhasil dihapus!');
    }

    private function findOwnedCompetition(int $id): Competition
    {
        return Competition::where('organizer_id', auth()->id())->findOrFail($id);
    }

    private function storePoster(UploadedFile $poster): string
    {
        $directory = public_path('uploads/posters');
        File::ensureDirectoryExists($directory);

        $filename = $poster->hashName();
        $poster->move($directory, $filename);

        return 'uploads/posters/'.$filename;
    }

    private function deletePoster(?string $posterPath): void
    {
        if ($posterPath && File::exists(public_path($posterPath))) {
            File::delete(public_path($posterPath));
        }
    }
}
