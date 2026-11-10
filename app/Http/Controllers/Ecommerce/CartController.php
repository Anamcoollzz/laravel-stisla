<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display cart page or return JSON for AJAX.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        if (! Auth::check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'unauthenticated',
                    'message' => 'Silakan login terlebih dahulu untuk mengakses keranjang belanja!',
                    'redirect' => route('ecommerce.login'),
                ], 401);
            }

            session()->flash('alert', 'Silakan login terlebih dahulu untuk mengakses keranjang belanja!');
            session()->flash('warning', 'Silakan login terlebih dahulu untuk mengakses keranjang belanja!');

            return redirect()->guest(route('ecommerce.login'));
        }

        $userId = (int) Auth::id();
        $cart = $this->getUserCartArray($userId);
        $subtotal = $this->calculateSubtotal($cart);
        $totalQuantity = $this->calculateTotalQuantity($cart);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'items' => array_values($cart),
                'count' => $totalQuantity,
                'subtotal' => $subtotal,
                'subtotal_formatted' => 'Rp '.number_format($subtotal, 0, ',', '.'),
                'html' => view('ecommerce.partials.cart-sidebar-items', [
                    'cartItems' => $cart,
                ])->render(),
            ]);
        }

        return view('ecommerce.pages.cart', [
            'cartItems' => $cart,
            'subtotal' => $subtotal,
            'totalQuantity' => $totalQuantity,
        ]);
    }

    /**
     * Add product to cart via AJAX.
     */
    public function add(Request $request, Product $product): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Silakan login terlebih dahulu untuk menambahkan produk ke keranjang belanja!',
                'redirect' => route('ecommerce.login'),
            ], 401);
        }

        $userId = (int) Auth::id();
        $quantity = max(1, (int) $request->input('quantity', 1));
        $productId = (int) $product->id;

        $cartRow = ProductCart::query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($cartRow) {
            $newQuantity = $cartRow->quantity + $quantity;
            if ($product->stock > 0 && $newQuantity > $product->stock) {
                $newQuantity = (int) $product->stock;
            }
            $cartRow->update(['quantity' => $newQuantity]);
        } else {
            $initialQty = $quantity;
            if ($product->stock > 0 && $initialQty > $product->stock) {
                $initialQty = (int) $product->stock;
            }

            ProductCart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $initialQty,
            ]);
        }

        $cart = $this->getUserCartArray($userId);
        $subtotal = $this->calculateSubtotal($cart);
        $totalQuantity = $this->calculateTotalQuantity($cart);

        return response()->json([
            'status' => 'success',
            'message' => 'Added to Cart successfully!',
            'count' => $totalQuantity,
            'subtotal' => $subtotal,
            'subtotal_formatted' => 'Rp '.number_format($subtotal, 0, ',', '.'),
            'product_id' => $productId,
            'html' => view('ecommerce.partials.cart-sidebar-items', [
                'cartItems' => $cart,
            ])->render(),
        ]);
    }

    /**
     * Update product quantity in cart.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Silakan login terlebih dahulu!',
                'redirect' => route('ecommerce.login'),
            ], 401);
        }

        $userId = (int) Auth::id();
        $quantity = (int) $request->input('quantity', 1);
        $productId = (int) $product->id;

        if ($quantity <= 0) {
            ProductCart::query()
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->delete();
        } else {
            $cartRow = ProductCart::query()
                ->where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($cartRow) {
                if ($product->stock > 0 && $quantity > $product->stock) {
                    $quantity = (int) $product->stock;
                }
                $cartRow->update(['quantity' => $quantity]);
            }
        }

        $cart = $this->getUserCartArray($userId);
        $subtotal = $this->calculateSubtotal($cart);
        $totalQuantity = $this->calculateTotalQuantity($cart);

        return response()->json([
            'status' => 'success',
            'message' => 'Cart updated successfully!',
            'count' => $totalQuantity,
            'subtotal' => $subtotal,
            'subtotal_formatted' => 'Rp '.number_format($subtotal, 0, ',', '.'),
            'product_id' => $productId,
            'html' => view('ecommerce.partials.cart-sidebar-items', [
                'cartItems' => $cart,
            ])->render(),
        ]);
    }

    /**
     * Remove product from cart.
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

        ProductCart::query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();

        $cart = $this->getUserCartArray($userId);
        $subtotal = $this->calculateSubtotal($cart);
        $totalQuantity = $this->calculateTotalQuantity($cart);

        return response()->json([
            'status' => 'success',
            'message' => 'Removed from Cart successfully!',
            'count' => $totalQuantity,
            'subtotal' => $subtotal,
            'subtotal_formatted' => 'Rp '.number_format($subtotal, 0, ',', '.'),
            'product_id' => $productId,
            'html' => view('ecommerce.partials.cart-sidebar-items', [
                'cartItems' => $cart,
            ])->render(),
        ]);
    }

    /**
     * Retrieve cart array for given user from database.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getUserCartArray(int $userId): array
    {
        $cartRows = ProductCart::query()
            ->where('user_id', $userId)
            ->with(['product' => fn ($q) => $q->active()])
            ->get();

        $cart = [];
        foreach ($cartRows as $row) {
            $product = $row->product;
            if (! $product) {
                continue;
            }

            $cart[$product->id] = [
                'id' => (int) $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'price' => (float) $product->final_price,
                'original_price' => (float) $product->price,
                'thumbnail' => $product->thumbnail_url,
                'quantity' => (int) $row->quantity,
                'stock' => (int) $product->stock,
            ];
        }

        return $cart;
    }

    /**
     * Calculate subtotal of cart items.
     *
     * @param  array<int, array<string, mixed>>  $cart
     */
    protected function calculateSubtotal(array $cart): float
    {
        $subtotal = 0.0;
        foreach ($cart as $item) {
            $subtotal += ((float) ($item['price'] ?? 0)) * ((int) ($item['quantity'] ?? 1));
        }

        return $subtotal;
    }

    /**
     * Calculate total items quantity in cart.
     *
     * @param  array<int, array<string, mixed>>  $cart
     */
    protected function calculateTotalQuantity(array $cart): int
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += (int) ($item['quantity'] ?? 1);
        }

        return $total;
    }
}
