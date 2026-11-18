<?php

namespace App\Http\Controllers\Admin\News;

use App\Exports\NewsCategoriesExport;
use App\Exports\NewsCategoriesTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\NewsCategoriesImport;
use App\Models\News\NewsCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class NewsCategoryController extends Controller
{
    /**
     * Display a listing of news categories.
     */
    public function index(): View
    {
        Gate::authorize('news-categories.view');

        $categories = NewsCategory::query()
            ->withCount('news')
            ->latest('id')
            ->get();

        return view('modules.news-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new news category.
     */
    public function create(): View
    {
        Gate::authorize('news-categories.create');

        return view('modules.news-categories.create');
    }

    /**
     * Store a newly created news category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('news-categories.create');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        NewsCategory::query()->create($validated);

        return redirect()
            ->route('news-categories.index')
            ->with('success', __('Kategori berita berhasil ditambahkan.'));
    }

    /**
     * Display the specified news category.
     */
    public function show(NewsCategory $newsCategory): View
    {
        Gate::authorize('news-categories.view');

        $newsCategory->load(['news' => fn ($q) => $q->latest('id')]);

        return view('modules.news-categories.show', compact('newsCategory'));
    }

    /**
     * Show the form for editing the specified news category.
     */
    public function edit(NewsCategory $newsCategory): View
    {
        Gate::authorize('news-categories.edit');

        return view('modules.news-categories.edit', compact('newsCategory'));
    }

    /**
     * Update the specified news category in storage.
     */
    public function update(Request $request, NewsCategory $newsCategory): RedirectResponse
    {
        Gate::authorize('news-categories.edit');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $newsCategory->update($validated);

        return redirect()
            ->route('news-categories.index')
            ->with('success', __('Kategori berita berhasil diperbarui.'));
    }

    /**
     * Remove the specified news category from storage.
     */
    public function destroy(NewsCategory $newsCategory): RedirectResponse
    {
        Gate::authorize('news-categories.delete');

        $newsCategory->delete();

        return redirect()
            ->route('news-categories.index')
            ->with('success', __('Kategori berita berhasil dihapus.'));
    }

    /**
     * Export news categories to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('news-categories.view');

        return Excel::download(new NewsCategoriesExport, 'news-categories-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Export news categories to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('news-categories.view');

        $categories = NewsCategory::query()->withCount('news')->latest('id')->get();
        $pdf = Pdf::loadView('modules.news-categories.pdf', compact('categories'));

        return $pdf->download('news-categories-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * Download sample Excel template for importing news categories.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('news-categories.create');

        return Excel::download(new NewsCategoriesTemplateExport, 'news-categories-template.xlsx');
    }

    /**
     * Import news categories from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('news-categories.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new NewsCategoriesImport, $request->file('file'));

        return redirect()
            ->route('news-categories.index')
            ->with('success', __('Kategori berita berhasil diimport.'));
    }
}
