<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Models\Blog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Display a listing of the blogs.
     */
    public function index(): View
    {
        Gate::authorize('blogs.view');

        $blogs = Blog::query()->orderBy('order')->latest('published_at')->latest('id')->get();

        $stats = [
            'total' => $blogs->count(),
            'active' => $blogs->where('is_active', true)->count(),
            'inactive' => $blogs->where('is_active', false)->count(),
        ];

        return view('modules.blogs.index', compact('blogs', 'stats'));
    }

    /**
     * Show the form for creating a new blog.
     */
    public function create(): View
    {
        Gate::authorize('blogs.create');

        return view('modules.blogs.create');
    }

    /**
     * Store a newly created blog in storage.
     */
    public function store(StoreBlogRequest $request): RedirectResponse
    {
        Gate::authorize('blogs.create');

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $data['order'] ?? (((int) Blog::max('order')) + 1);
        $data['author_name'] = $data['author_name'] ?: ($request->user()?->name ?? 'Admin');
        $data['published_at'] = $data['published_at'] ?: now()->toDateString();

        Blog::create($data);

        return redirect()->route('blogs.index')->with('success', __('Blog post created successfully.'));
    }

    /**
     * Display the specified blog.
     */
    public function show(Blog $blog): View
    {
        Gate::authorize('blogs.view');

        return view('modules.blogs.show', compact('blog'));
    }

    /**
     * Show the form for editing the specified blog.
     */
    public function edit(Blog $blog): View
    {
        Gate::authorize('blogs.edit');

        return view('modules.blogs.edit', compact('blog'));
    }

    /**
     * Update the specified blog in storage.
     */
    public function update(UpdateBlogRequest $request, Blog $blog): RedirectResponse
    {
        Gate::authorize('blogs.edit');

        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($blog->image && Storage::disk('public')->exists($blog->image)) {
                Storage::disk('public')->delete($blog->image);
            }
            $data['image'] = $request->file('image')->store('blogs', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($blog->image && Storage::disk('public')->exists($blog->image)) {
                Storage::disk('public')->delete($blog->image);
            }
            $data['image'] = null;
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $blog->update($data);

        return redirect()->route('blogs.index')->with('success', __('Blog post updated successfully.'));
    }

    /**
     * Remove the specified blog from storage.
     */
    public function destroy(Blog $blog): RedirectResponse
    {
        Gate::authorize('blogs.delete');

        if ($blog->image && Storage::disk('public')->exists($blog->image)) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return redirect()->route('blogs.index')->with('success', __('Blog post deleted successfully.'));
    }

    /**
     * Duplicate the specified blog.
     */
    public function duplicate(Blog $blog): RedirectResponse
    {
        Gate::authorize('blogs.create');

        $clone = $blog->replicate();
        $clone->title = $clone->title.' (Copy)';
        $clone->slug = null; // Will trigger boot auto-slug
        $clone->order = ((int) Blog::max('order')) + 1;
        $clone->save();

        return redirect()->route('blogs.index')->with('success', __('Blog post duplicated successfully.'));
    }
}
