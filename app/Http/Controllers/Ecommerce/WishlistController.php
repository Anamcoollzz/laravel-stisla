<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Session key for wishlist product IDs.
     */
    public const SESSION_KEY = 'ecommerce_wishlist';

    /**
     * Display wishlist page or return JSON for AJAX.
     */
    public function index(Request $request): View|JsonResponse
    {
        $wishlistIds = $this->getWishlistIds();
        $products = Product::query()
            ->active()
            ->with(['category', 'brand', 'size', 'color', 'images'])
            ->whereIn('id', $wishlistIds)
            ->get();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'count' => $products->count(),
                'html' => view('ecommerce.partials.wishlist-sidebar-items', [
                    'wishlistProducts' => $products,
                ])->render(),
            ]);
        }

        return view('ecommerce.pages.wishlist', [
            'products' => $products,
        ]);
    }

    /**
     * Toggle product in wishlist via AJAX.
     */
    public function toggle(Request $request, Product $product): JsonResponse
    {
        $wishlistIds = $this->getWishlistIds();
        $productId = (int) $product->id;

        if (in_array($productId, $wishlistIds, true)) {
            $wishlistIds = array_values(array_diff($wishlistIds, [$productId]));
            $isWishlisted = false;
            $message = 'Removed from Wishlist successfully!';
        } else {
            $wishlistIds[] = $productId;
            $isWishlisted = true;
            $message = 'Added to Wishlist successfully!';
        }

        session()->put(self::SESSION_KEY, $wishlistIds);

        $wishlistProducts = Product::query()
            ->active()
            ->with(['category', 'brand', 'size', 'color', 'images'])
            ->whereIn('id', $wishlistIds)
            ->get();

        return response()->json([
            'status' => 'success',
            'action' => $isWishlisted ? 'added' : 'removed',
            'is_wishlisted' => $isWishlisted,
            'message' => $message,
            'count' => count($wishlistIds),
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
        $wishlistIds = $this->getWishlistIds();
        $productId = (int) $product->id;

        $wishlistIds = array_values(array_diff($wishlistIds, [$productId]));
        session()->put(self::SESSION_KEY, $wishlistIds);

        $wishlistProducts = Product::query()
            ->active()
            ->with(['category', 'brand', 'size', 'color', 'images'])
            ->whereIn('id', $wishlistIds)
            ->get();

        return response()->json([
            'status' => 'success',
            'action' => 'removed',
            'is_wishlisted' => false,
            'message' => 'Removed from Wishlist successfully!',
            'count' => count($wishlistIds),
            'product_id' => $productId,
            'html' => view('ecommerce.partials.wishlist-sidebar-items', [
                'wishlistProducts' => $wishlistProducts,
            ])->render(),
        ]);
    }

    /**
     * Get array of integer product IDs currently in wishlist.
     *
     * @return array<int>
     */
    protected function getWishlistIds(): array
    {
        $ids = session()->get(self::SESSION_KEY, []);

        return array_map('intval', (array) $ids);
    }
}
