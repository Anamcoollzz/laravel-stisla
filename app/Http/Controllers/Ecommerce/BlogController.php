<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\View\View;

class BlogController extends Controller
{
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

