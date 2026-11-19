<?php

namespace App\Http\Controllers\Admin\News;

use App\Exports\NewsTagsExport;
use App\Exports\NewsTagsTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\NewsTagsImport;
use App\Models\News\NewsTag;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class NewsTagController extends Controller
{
    /**
     * Display a listing of news tags.
     */
    public function index(): View
    {
        Gate::authorize('news-tags.view');

        $tags = NewsTag::query()
            ->withCount('news')
            ->latest('id')
            ->get();

        return view('modules.news-tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new news tag.
     */
    public function create(): View
    {
        Gate::authorize('news-tags.create');

        return view('modules.news-tags.create');
    }

    /**
     * Store a newly created news tag in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('news-tags.create');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:news_tags,slug'],
        ]);

        $slug = filled($validated['slug'] ?? null)
            ? Str::slug((string) $validated['slug'])
            : $this->generateUniqueSlug($validated['name']);

        NewsTag::query()->create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return redirect()
            ->route('news-tags.index')
            ->with('success', __('Tag berita berhasil ditambahkan.'));
    }

    /**
     * Display the specified news tag.
     */
    public function show(NewsTag $newsTag): View
    {
        Gate::authorize('news-tags.view');

        $newsTag->load(['news' => fn ($q) => $q->latest('id')]);

        return view('modules.news-tags.show', compact('newsTag'));
    }

    /**
     * Show the form for editing the specified news tag.
     */
    public function edit(NewsTag $newsTag): View
    {
        Gate::authorize('news-tags.edit');

        return view('modules.news-tags.edit', compact('newsTag'));
    }

    /**
     * Update the specified news tag in storage.
     */
    public function update(Request $request, NewsTag $newsTag): RedirectResponse
    {
        Gate::authorize('news-tags.edit');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('news_tags', 'slug')->ignore($newsTag->id)],
        ]);

        $slug = filled($validated['slug'] ?? null)
            ? Str::slug((string) $validated['slug'])
            : $this->generateUniqueSlug($validated['name'], $newsTag->id);

        $newsTag->update([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return redirect()
            ->route('news-tags.index')
            ->with('success', __('Tag berita berhasil diperbarui.'));
    }

    /**
     * Remove the specified news tag from storage.
     */
    public function destroy(NewsTag $newsTag): RedirectResponse
    {
        Gate::authorize('news-tags.delete');

        $newsTag->delete();

        return redirect()
            ->route('news-tags.index')
            ->with('success', __('Tag berita berhasil dihapus.'));
    }

    /**
     * Export news tags to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('news-tags.view');

        return Excel::download(new NewsTagsExport, 'news-tags-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Export news tags to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('news-tags.view');

        $tags = NewsTag::query()->withCount('news')->latest('id')->get();
        $pdf = Pdf::loadView('modules.news-tags.pdf', compact('tags'));

        return $pdf->download('news-tags-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * Download sample Excel template for importing news tags.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('news-tags.create');

        return Excel::download(new NewsTagsTemplateExport, 'news-tags-template.xlsx');
    }

    /**
     * Import news tags from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('news-tags.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new NewsTagsImport, $request->file('file'));

        return redirect()
            ->route('news-tags.index')
            ->with('success', __('Tag berita berhasil diimport.'));
    }

    /**
     * Generate a unique slug for news tag.
     */
    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'tag';
        $slug = $base;
        $counter = 1;

        while (
            NewsTag::query()
                ->where('slug', $slug)
                ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
