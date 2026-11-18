<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Models\News\News;
use App\Models\News\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Display all published news articles with search, category filter, and pagination.
     */
    public function all(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $selectedCategory = trim((string) $request->query('category', ''));

        $newsList = News::query()
            ->published()
            ->with(['category', 'publisher'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($selectedCategory !== '', fn ($q) => $q->where('news_category_id', $selectedCategory))
            ->orderByDesc('published_at')
            ->latest('id')
            ->paginate(5)
            ->withQueryString();

        $categories = NewsCategory::query()
            ->withCount(['news' => fn ($q) => $q->published()])
            ->orderBy('name')
            ->get();

        $recentPosts = News::query()
            ->published()
            ->orderByDesc('published_at')
            ->latest('id')
            ->limit(4)
            ->get();

        return view('news.all', compact(
            'newsList',
            'categories',
            'recentPosts',
            'search',
            'selectedCategory'
        ));
    }
}
