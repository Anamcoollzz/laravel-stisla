<?php

namespace App\Http\Controllers\Admin\News;

use App\Exports\NewsExport;
use App\Exports\NewsTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\NewsImport;
use App\Models\News\News;
use App\Models\News\NewsCategory;
use App\Models\News\NewsTag;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class NewsController extends Controller
{
    /**
     * Display a listing of news articles.
     */
    public function index(Request $request): View
    {
        Gate::authorize('news.view');

        $selectedStatus = trim((string) $request->query('status', ''));
        $selectedCategory = trim((string) $request->query('news_category_id', ''));

        $newsList = News::query()
            ->with(['category', 'publisher', 'tags'])
            ->when($selectedStatus !== '', fn ($q) => $q->where('status', $selectedStatus))
            ->when($selectedCategory !== '', fn ($q) => $q->where('news_category_id', $selectedCategory))
            ->latest('id')
            ->get();

        $categories = NewsCategory::query()->orderBy('name')->get();

        return view('modules.news.index', compact(
            'newsList',
            'categories',
            'selectedStatus',
            'selectedCategory'
        ));
    }

    /**
     * Show the form for creating a new news article.
     */
    public function create(): View
    {
        Gate::authorize('news.create');

        $categories = NewsCategory::query()->orderBy('name')->get();
        $publishers = User::query()->orderBy('name')->get(['id', 'name', 'email']);
        $tags = NewsTag::query()->orderBy('name')->get();

        return view('modules.news.create', compact('categories', 'publishers', 'tags'));
    }

    /**
     * Store a newly created news article in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('news.create');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:news,slug'],
            'news_category_id' => ['nullable', 'integer', Rule::exists('news_categories', 'id')],
            'publisher_user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'content' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'thumbnail_mini' => ['nullable', 'image', 'max:1024'],
            'published_at' => ['nullable', 'date'],
            'status' => ['required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', Rule::exists('news_tags', 'id')],
        ]);

        $slug = filled($validated['slug'] ?? null)
            ? Str::slug((string) $validated['slug'])
            : $this->generateUniqueSlug($validated['title']);

        $thumbnailPath = $request->hasFile('thumbnail')
            ? $request->file('thumbnail')->store('news/thumbnails', 'public')
            : null;

        $thumbnailMiniPath = $request->hasFile('thumbnail_mini')
            ? $request->file('thumbnail_mini')->store('news/thumbnails-mini', 'public')
            : null;

        $publishedAt = $validated['published_at'] ?? null;
        if ($validated['status'] === 'published' && blank($publishedAt)) {
            $publishedAt = now();
        }

        $news = News::query()->create([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'] ?? null,
            'thumbnail' => $thumbnailPath,
            'thumbnail_mini' => $thumbnailMiniPath,
            'published_at' => $publishedAt,
            'status' => $validated['status'],
            'news_category_id' => $validated['news_category_id'] ?? null,
            'publisher_user_id' => $validated['publisher_user_id'] ?? Auth::id(),
        ]);

        $news->tags()->sync($validated['tag_ids'] ?? []);

        return redirect()
            ->route('admin.news.index')
            ->with('success', __('Berita berhasil ditambahkan.'));
    }

    /**
     * Display the specified news article.
     */
    public function show(News $news): View
    {
        Gate::authorize('news.view');

        $news->load(['category', 'publisher', 'tags']);

        return view('modules.news.show', compact('news'));
    }

    /**
     * Show the form for editing the specified news article.
     */
    public function edit(News $news): View
    {
        Gate::authorize('news.edit');

        $news->load('tags');
        $categories = NewsCategory::query()->orderBy('name')->get();
        $publishers = User::query()->orderBy('name')->get(['id', 'name', 'email']);
        $tags = NewsTag::query()->orderBy('name')->get();

        return view('modules.news.edit', compact('news', 'categories', 'publishers', 'tags'));
    }

    /**
     * Update the specified news article in storage.
     */
    public function update(Request $request, News $news): RedirectResponse
    {
        Gate::authorize('news.edit');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('news', 'slug')->ignore($news->id)],
            'news_category_id' => ['nullable', 'integer', Rule::exists('news_categories', 'id')],
            'publisher_user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'content' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'thumbnail_mini' => ['nullable', 'image', 'max:1024'],
            'published_at' => ['nullable', 'date'],
            'status' => ['required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', Rule::exists('news_tags', 'id')],
        ]);

        $slug = filled($validated['slug'] ?? null)
            ? Str::slug((string) $validated['slug'])
            : $this->generateUniqueSlug($validated['title'], $news->id);

        $thumbnailPath = $news->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($news->thumbnail && ! str_starts_with($news->thumbnail, 'http')) {
                Storage::disk('public')->delete($news->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('news/thumbnails', 'public');
        }

        $thumbnailMiniPath = $news->thumbnail_mini;
        if ($request->hasFile('thumbnail_mini')) {
            if ($news->thumbnail_mini && ! str_starts_with($news->thumbnail_mini, 'http')) {
                Storage::disk('public')->delete($news->thumbnail_mini);
            }
            $thumbnailMiniPath = $request->file('thumbnail_mini')->store('news/thumbnails-mini', 'public');
        }

        $publishedAt = $validated['published_at'] ?? $news->published_at;
        if ($validated['status'] === 'published' && blank($publishedAt)) {
            $publishedAt = now();
        }

        $news->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'] ?? null,
            'thumbnail' => $thumbnailPath,
            'thumbnail_mini' => $thumbnailMiniPath,
            'published_at' => $publishedAt,
            'status' => $validated['status'],
            'news_category_id' => $validated['news_category_id'] ?? null,
            'publisher_user_id' => $validated['publisher_user_id'] ?? $news->publisher_user_id,
        ]);

        $news->tags()->sync($validated['tag_ids'] ?? []);

        return redirect()
            ->route('admin.news.index')
            ->with('success', __('Berita berhasil diperbarui.'));
    }

    /**
     * Remove the specified news article from storage.
     */
    public function destroy(News $news): RedirectResponse
    {
        Gate::authorize('news.delete');

        if ($news->thumbnail && ! str_starts_with($news->thumbnail, 'http')) {
            Storage::disk('public')->delete($news->thumbnail);
        }

        if ($news->thumbnail_mini && ! str_starts_with($news->thumbnail_mini, 'http')) {
            Storage::disk('public')->delete($news->thumbnail_mini);
        }

        $news->delete();

        return redirect()
            ->route('admin.news.index')
            ->with('success', __('Berita berhasil dihapus.'));
    }

    /**
     * Export news articles to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('news.view');

        return Excel::download(new NewsExport, 'news-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Export news articles to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('news.view');

        $newsList = News::query()->with(['category', 'publisher'])->latest('id')->get();
        $pdf = Pdf::loadView('modules.news.pdf', compact('newsList'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('news-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * Download sample Excel template for importing news.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('news.create');

        return Excel::download(new NewsTemplateExport, 'news-template.xlsx');
    }

    /**
     * Import news articles from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('news.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new NewsImport, $request->file('file'));

        return redirect()
            ->route('admin.news.index')
            ->with('success', __('Berita berhasil diimport.'));
    }

    /**
     * Generate a unique slug for news.
     */
    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'news';
        $slug = $base;
        $counter = 1;

        while (
            News::query()
                ->where('slug', $slug)
                ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
