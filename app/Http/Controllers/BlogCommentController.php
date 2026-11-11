<?php

namespace App\Http\Controllers;

use App\Models\BlogComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BlogCommentController extends Controller
{
    /**
     * Display a listing of blog comments.
     */
    public function index(Request $request): View
    {
        Gate::authorize('blog-comments.view');

        $query = BlogComment::query()->with(['blog', 'parent'])->latest('id');

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('comment', 'like', "%{$search}%")
                    ->orWhereHas('blog', function ($bq) use ($search) {
                        $bq->where('title', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        $comments = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => BlogComment::count(),
            'approved' => BlogComment::where('is_approved', true)->count(),
            'pending' => BlogComment::where('is_approved', false)->count(),
        ];

        return view('modules.blog-comments.index', compact('comments', 'stats'));
    }

    /**
     * Display the specified blog comment.
     */
    public function show(BlogComment $blogComment): View
    {
        Gate::authorize('blog-comments.view');

        $blogComment->load(['blog', 'parent', 'replies']);

        return view('modules.blog-comments.show', compact('blogComment'));
    }

    /**
     * Toggle comment approval status.
     */
    public function toggleApproval(BlogComment $blogComment): RedirectResponse
    {
        Gate::authorize('blog-comments.edit');

        $blogComment->update([
            'is_approved' => ! $blogComment->is_approved,
        ]);

        $message = $blogComment->is_approved
            ? __('Comment has been approved.')
            : __('Comment has been unapproved.');

        return back()->with('success', $message);
    }

    /**
     * Remove the specified blog comment from storage.
     */
    public function destroy(BlogComment $blogComment): RedirectResponse
    {
        Gate::authorize('blog-comments.delete');

        $blogComment->delete();

        return redirect()->route('blog-comments.index')->with('success', __('Comment deleted successfully.'));
    }
}
