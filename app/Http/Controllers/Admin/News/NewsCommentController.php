<?php

namespace App\Http\Controllers\Admin\News;

use App\Exports\NewsCommentsExport;
use App\Exports\NewsCommentsTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\NewsCommentsImport;
use App\Models\News\NewsComment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class NewsCommentController extends Controller
{
    /**
     * Display a listing of news comments.
     */
    public function index(Request $request): View
    {
        Gate::authorize('news-comments.view');

        $selectedStatus = trim((string) $request->query('status', ''));

        $comments = NewsComment::query()
            ->with(['news', 'parent', 'user'])
            ->when($selectedStatus === 'approved', fn ($q) => $q->where('is_approved', true))
            ->when($selectedStatus === 'pending', fn ($q) => $q->where('is_approved', false))
            ->latest('id')
            ->get();

        $stats = [
            'total' => NewsComment::count(),
            'approved' => NewsComment::where('is_approved', true)->count(),
            'pending' => NewsComment::where('is_approved', false)->count(),
        ];

        return view('modules.news-comments.index', compact('comments', 'stats', 'selectedStatus'));
    }

    /**
     * Display the specified news comment and replies.
     */
    public function show(NewsComment $newsComment): View
    {
        Gate::authorize('news-comments.view');

        $newsComment->load(['news', 'parent', 'replies.user', 'user']);

        return view('modules.news-comments.show', compact('newsComment'));
    }

    /**
     * Toggle verification / approval status for a comment.
     */
    public function toggleApproval(NewsComment $newsComment): RedirectResponse
    {
        Gate::authorize('news-comments.edit');

        $newsComment->update([
            'is_approved' => ! $newsComment->is_approved,
        ]);

        $message = $newsComment->is_approved
            ? __('Komentar berita berhasil diverifikasi.')
            : __('Verifikasi komentar berita dibatalkan.');

        return back()->with('success', $message);
    }

    /**
     * Store an admin reply to a news comment (auto-verified).
     */
    public function reply(Request $request, NewsComment $newsComment): RedirectResponse
    {
        Gate::authorize('news-comments.create');

        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:3000'],
        ]);

        $user = Auth::user();
        $parentId = $newsComment->parent_id ?? $newsComment->id;

        NewsComment::query()->create([
            'news_id' => $newsComment->news_id,
            'user_id' => $user?->id,
            'parent_id' => $parentId,
            'name' => $user?->name ?? 'Admin',
            'email' => $user?->email ?? 'admin@example.com',
            'comment' => $validated['comment'],
            'is_approved' => true,
        ]);

        if (! $newsComment->is_approved) {
            $newsComment->update(['is_approved' => true]);
        }

        return back()->with('success', __('Balasan komentar berhasil dikirim.'));
    }

    /**
     * Remove the specified news comment from storage.
     */
    public function destroy(NewsComment $newsComment): RedirectResponse
    {
        Gate::authorize('news-comments.delete');

        $newsComment->delete();

        return redirect()
            ->route('news-comments.index')
            ->with('success', __('Komentar berita berhasil dihapus.'));
    }

    /**
     * Export news comments to Excel.
     */
    public function export(): BinaryFileResponse
    {
        Gate::authorize('news-comments.view');

        return Excel::download(new NewsCommentsExport, 'news-comments-'.now()->format('Y-m-d-His').'.xlsx');
    }

    /**
     * Export news comments to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('news-comments.view');

        $comments = NewsComment::query()->with(['news', 'parent'])->latest('id')->get();
        $pdf = Pdf::loadView('modules.news-comments.pdf', compact('comments'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('news-comments-'.now()->format('Y-m-d-His').'.pdf');
    }

    /**
     * Download sample Excel template for importing news comments.
     */
    public function template(): BinaryFileResponse
    {
        Gate::authorize('news-comments.create');

        return Excel::download(new NewsCommentsTemplateExport, 'news-comments-template.xlsx');
    }

    /**
     * Import news comments from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        Gate::authorize('news-comments.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Excel::import(new NewsCommentsImport, $request->file('file'));

        return redirect()
            ->route('news-comments.index')
            ->with('success', __('Komentar berita berhasil diimport.'));
    }
}
