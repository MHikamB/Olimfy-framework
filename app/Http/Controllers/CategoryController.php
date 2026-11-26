<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Competition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Tampilkan daftar kategori dengan pencarian dan pagination.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $categories = Category::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('nama_kategori', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama_kategori')
            ->paginate(10)
            ->withQueryString();

        return view('penyelenggara.categories.index', compact('categories', 'search'));
    }


    public function create(): View
    {
        return view('penyelenggara.categories.create');
    }


    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return redirect()
            ->route('penyelenggara.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan!');
    }


    public function edit(int $id): View
    {
        $category = Category::findOrFail($id);

        return view('penyelenggara.categories.edit', compact('category'));
    }


    public function update(UpdateCategoryRequest $request, int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $category->update($request->validated());

        return redirect()
            ->route('penyelenggara.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui!');
    }


    public function destroy(int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        if ($this->isUsedByCompetition($category)) {
            return redirect()
                ->back()
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data kompetisi.');
        }

        $category->delete();

        return redirect()
            ->route('penyelenggara.kategori.index')
            ->with('success', 'Kategori berhasil dihapus!');
    }


    private function isUsedByCompetition(Category $category): bool
    {
        return Competition::query()
            ->where('kategori', $category->nama_kategori)
            ->when(
                Schema::hasColumn('competitions', 'category_id'),
                fn (Builder $query) => $query->orWhere('category_id', $category->id),
            )
            ->exists();
    }
}
