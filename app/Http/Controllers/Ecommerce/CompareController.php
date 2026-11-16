<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCompare;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CompareController extends Controller
{
    /**
     * Scope ProductCompare query to the current user or guest session.
     *
     * @return Builder<ProductCompare>
     */
    protected function compareQuery(Request $request): Builder
    {
        if (Auth::check()) {
            return ProductCompare::query()->where('user_id', (int) Auth::id());
        }

        return ProductCompare::query()
            ->whereNull('user_id')
            ->where('session_id', $request->session()->getId());
    }

    /**
     * Display the product comparison page.
     */
    public function index(Request $request): View
    {
        $compares = $this->compareQuery($request)
            ->with([
                'product' => fn ($q) => $q->active()
                    ->with(['category', 'brand', 'vendor', 'size', 'color', 'images'])
                    ->withCount('approvedReviews')
                    ->withAvg('approvedReviews', 'rating'),
            ])
            ->latest('id')
            ->get()
            ->filter(fn (ProductCompare $compare) => $compare->product !== null)
            ->values();

        return view('ecommerce.pages.compare', compact('compares'));
    }

    /**
     * Toggle a product in the compare list via AJAX.
     */
    public function toggle(Request $request, Product $product): JsonResponse
    {
        $existing = $this->compareQuery($request)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $isCompared = false;
            $message = __('Removed from Compare list successfully!');
        } else {
            ProductCompare::create([
                'user_id' => Auth::check() ? (int) Auth::id() : null,
                'session_id' => Auth::check() ? null : $request->session()->getId(),
                'product_id' => $product->id,
                'location' => 'In Store, Online',
                'quantity' => 1,
            ]);
            $isCompared = true;
            $message = __('Added to Compare list successfully!');
        }

        $count = $this->compareQuery($request)->count();

        return response()->json([
            'status' => 'success',
            'is_compared' => $isCompared,
            'count' => $count,
            'message' => $message,
        ]);
    }

    /**
     * Remove a product from the compare list.
     */
    public function remove(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        $this->compareQuery($request)
            ->where('product_id', $product->id)
            ->delete();

        $count = $this->compareQuery($request)->count();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'count' => $count,
                'message' => __('Removed from Compare list successfully!'),
            ]);
        }

        return redirect()->route('ecommerce.compare')->with('success', __('Product removed from compare list.'));
    }
}
