<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Models\News\News;
use App\Models\News\NewsCategory;
use App\Models\News\NewsComment;
use App\Models\News\NewsTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Display all published news articles with search, category/tag filter, and pagination.
     */
    public function all(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $selectedCategory = trim((string) $request->query('category', ''));
        $selectedTag = trim((string) $request->query('tag', ''));

        $newsList = News::query()
            ->published()
            ->with(['category', 'publisher', 'tags'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($selectedCategory !== '', fn ($q) => $q->where('news_category_id', $selectedCategory))
            ->when($selectedTag !== '', fn ($q) => $q->whereHas('tags', fn ($t) => $t->where('slug', $selectedTag)))
            ->orderByDesc('published_at')
            ->latest('id')
            ->paginate(5)
            ->withQueryString();

        $categories = NewsCategory::query()
            ->withCount(['news' => fn ($q) => $q->published()])
            ->orderBy('name')
            ->get();

        $tags = NewsTag::query()
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
            'tags',
            'recentPosts',
            'search',
            'selectedCategory',
            'selectedTag'
        ));
    }

    /**
     * Display a single published news article by slug.
     */
    public function show(string $slug): View
    {
        $news = News::query()
            ->published()
            ->with(['category', 'publisher', 'tags', 'approvedTopLevelComments'])
            ->where('slug', $slug)
            ->firstOrFail();

        $prevPost = News::query()
            ->published()
            ->where('id', '<', $news->id)
            ->orderByDesc('id')
            ->first();

        $nextPost = News::query()
            ->published()
            ->where('id', '>', $news->id)
            ->orderBy('id')
            ->first();

        $categories = NewsCategory::query()
            ->withCount(['news' => fn ($q) => $q->published()])
            ->orderBy('name')
            ->get();

        $tags = NewsTag::query()
            ->withCount(['news' => fn ($q) => $q->published()])
            ->orderBy('name')
            ->get();

        $recentPosts = News::query()
            ->published()
            ->where('id', '!=', $news->id)
            ->orderByDesc('published_at')
            ->latest('id')
            ->limit(4)
            ->get();

        return view('news.show', compact(
            'news',
            'prevPost',
            'nextPost',
            'categories',
            'tags',
            'recentPosts'
        ));
    }

    /**
     * Store a new public comment on a news article (pending admin verification).
     */
    public function storeComment(Request $request, string $slug): RedirectResponse
    {
        $news = News::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'website' => ['nullable', 'string', 'max:255'],
            'comment' => ['required', 'string', 'max:3000'],
            'parent_id' => ['nullable', 'integer', Rule::exists('news_comments', 'id')],
            'captcha' => ['required', 'captcha'],
        ], [
            'captcha.captcha' => __('Kode captcha tidak sesuai.'),
        ]);

        NewsComment::query()->create([
            'news_id' => $news->id,
            'user_id' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'website' => $validated['website'] ?? null,
            'comment' => $validated['comment'],
            'is_approved' => false,
        ]);

        return redirect()
            ->to(route('news.show', $news->slug).'#commentsArea')
            ->with('comment_success', __('Komentar Anda berhasil dikirim dan sedang menunggu verifikasi admin.'));
    }
}
