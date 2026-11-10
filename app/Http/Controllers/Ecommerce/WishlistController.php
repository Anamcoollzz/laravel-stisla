<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductWishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Display wishlist page or return JSON for AJAX.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        if (! Auth::check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'unauthenticated',
                    'message' => 'Silakan login terlebih dahulu untuk mengakses wishlist!',
                    'redirect' => route('ecommerce.login'),
                ], 401);
            }

            session()->flash('alert', 'Silakan login terlebih dahulu untuk mengakses wishlist!');
            session()->flash('warning', 'Silakan login terlebih dahulu untuk mengakses wishlist!');

            return redirect()->guest(route('ecommerce.login'));
        }

        $userId = (int) Auth::id();
        $wishlistProducts = ProductWishlist::query()
            ->where('user_id', $userId)
            ->with(['product' => fn ($q) => $q->active()->with(['category', 'brand', 'size', 'color', 'images'])])
            ->latest('id')
            ->get()
            ->pluck('product')
            ->filter()
            ->values();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'count' => $wishlistProducts->count(),
                'html' => view('ecommerce.partials.wishlist-sidebar-items', [
                    'wishlistProducts' => $wishlistProducts,
                ])->render(),
            ]);
        }

        return view('ecommerce.pages.wishlist', [
            'products' => $wishlistProducts,
        ]);
    }

    /**
     * Toggle product in wishlist via AJAX.
     */
    public function toggle(Request $request, Product $product): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Silakan login terlebih dahulu untuk menambahkan produk ke wishlist!',
                'redirect' => route('ecommerce.login'),
            ], 401);
        }

        $userId = (int) Auth::id();
        $productId = (int) $product->id;

        $existing = ProductWishlist::query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $isWishlisted = false;
            $message = 'Removed from Wishlist successfully!';
        } else {
            ProductWishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            $isWishlisted = true;
            $message = 'Added to Wishlist successfully!';
        }

        $wishlistProducts = ProductWishlist::query()
            ->where('user_id', $userId)
            ->with(['product' => fn ($q) => $q->active()->with(['category', 'brand', 'size', 'color', 'images'])])
            ->latest('id')
            ->get()
            ->pluck('product')
            ->filter()
            ->values();

        return response()->json([
            'status' => 'success',
            'action' => $isWishlisted ? 'added' : 'removed',
            'is_wishlisted' => $isWishlisted,
            'message' => $message,
            'count' => $wishlistProducts->count(),
            'product_id' => $productId,
            'html' => view('ecommerce.partials.wishlist-sidebar-items', [
                'wishlistProducts' => $wishlistProducts,
            ])->render(),
        ]);
    }

    /**
     * Remove specific product from wishlist.
     */
    public function remove(Request $request, Product $product): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Silakan login terlebih dahulu!',
                'redirect' => route('ecommerce.login'),
            ], 401);
        }

        $userId = (int) Auth::id();
        $productId = (int) $product->id;

        ProductWishlist::query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();

        $wishlistProducts = ProductWishlist::query()
            ->where('user_id', $userId)
            ->with(['product' => fn ($q) => $q->active()->with(['category', 'brand', 'size', 'color', 'images'])])
            ->latest('id')
            ->get()
            ->pluck('product')
            ->filter()
            ->values();

        return response()->json([
            'status' => 'success',
            'action' => 'removed',
            'is_wishlisted' => false,
            'message' => 'Removed from Wishlist successfully!',
            'count' => $wishlistProducts->count(),
            'product_id' => $productId,
            'html' => view('ecommerce.partials.wishlist-sidebar-items', [
                'wishlistProducts' => $wishlistProducts,
            ])->render(),
        ]);
    }
}
