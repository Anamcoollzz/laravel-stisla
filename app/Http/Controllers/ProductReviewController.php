<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProductReviewController extends Controller
{
    /**
     * Display a listing of product reviews.
     */
    public function index(Request $request): View
    {
        Gate::authorize('product-reviews.view');

        $query = ProductReview::query()->with(['product', 'user'])->latest('id');

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('comment', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('title', 'like', "%{$search}%");
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

        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->input('rating'));
        }

        $reviews = $query->get();

        $stats = [
            'total' => ProductReview::count(),
            'approved' => ProductReview::where('is_approved', true)->count(),
            'pending' => ProductReview::where('is_approved', false)->count(),
            'average_rating' => round((float) (ProductReview::where('is_approved', true)->avg('rating') ?? 0), 1),
        ];

        return view('modules.product-reviews.index', compact('reviews', 'stats'));
    }

    /**
     * Display the specified product review.
     */
    public function show(ProductReview $productReview): View
    {
        Gate::authorize('product-reviews.view');

        $productReview->load(['product.category', 'user']);

        return view('modules.product-reviews.show', compact('productReview'));
    }

    /**
     * Toggle review approval status.
     */
    public function toggleApproval(ProductReview $productReview): RedirectResponse
    {
        Gate::authorize('product-reviews.edit');

        $productReview->update([
            'is_approved' => ! $productReview->is_approved,
        ]);

        $message = $productReview->is_approved
            ? __('Ulasan produk berhasil disetujui.')
            : __('Persetujuan ulasan produk berhasil dibatalkan.');

        return back()->with('success', $message);
    }

    /**
     * Remove the specified product review from storage.
     */
    public function destroy(ProductReview $productReview): RedirectResponse
    {
        Gate::authorize('product-reviews.delete');

        $productReview->delete();

        return redirect()->route('product-reviews.index')->with('success', __('Ulasan produk berhasil dihapus.'));
    }
}

