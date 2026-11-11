<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Display a listing of blogs with search and category filtering.
     */
    public function index(Request $request): View
    {
        $query = Blog::query()->active();

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($category = trim((string) $request->input('category'))) {
            $query->where('category', $category);
        }

        $blogs = $query->orderBy('order')
            ->latest('published_at')
            ->latest('id')
            ->paginate(9)
            ->withQueryString();

        $categories = Blog::query()
            ->active()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->select('category')
            ->selectRaw('count(*) as count')
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        return view('ecommerce.pages.blog-full-width', compact('blogs', 'categories'));
    }

    /**
     * Display the blog detail page.
     */
    public function show(string $slug): View
    {
        $blog = Blog::query()
            ->where('slug', $slug)
            ->firstOrFail();

        $recentBlogs = Blog::query()
            ->active()
            ->where('id', '!=', $blog->id)
            ->orderBy('order')
            ->latest('published_at')
            ->latest('id')
            ->take(5)
            ->get();

        $categories = Blog::query()
            ->active()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->select('category')
            ->selectRaw('count(*) as count')
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        return view('ecommerce.pages.blog-detail', compact('blog', 'recentBlogs', 'categories'));
    }
}
