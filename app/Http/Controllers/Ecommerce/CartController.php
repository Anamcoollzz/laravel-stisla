<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Session key for cart data.
     */
    public const SESSION_KEY = 'ecommerce_cart';

    /**
     * Display cart page or return JSON for AJAX.
     */
    public function index(Request $request): View|JsonResponse
    {
        $cart = $this->getCart();
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
        $quantity = max(1, (int) $request->input('quantity', 1));
        $cart = $this->getCart();
        $productId = (int) $product->id;

        if (isset($cart[$productId])) {
            $newQuantity = $cart[$productId]['quantity'] + $quantity;
            if ($product->stock > 0 && $newQuantity > $product->stock) {
                $newQuantity = $product->stock;
            }
            $cart[$productId]['quantity'] = $newQuantity;
        } else {
            $initialQty = $quantity;
            if ($product->stock > 0 && $initialQty > $product->stock) {
                $initialQty = $product->stock;
            }

            $cart[$productId] = [
                'id' => $productId,
                'title' => $product->title,
                'slug' => $product->slug,
                'price' => (float) $product->final_price,
                'original_price' => (float) $product->price,
                'thumbnail' => $product->thumbnail_url,
                'quantity' => $initialQty,
                'stock' => (int) $product->stock,
            ];
        }

        session()->put(self::SESSION_KEY, $cart);

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
        $quantity = (int) $request->input('quantity', 1);
        $cart = $this->getCart();
        $productId = (int) $product->id;

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } elseif (isset($cart[$productId])) {
            if ($product->stock > 0 && $quantity > $product->stock) {
                $quantity = $product->stock;
            }
            $cart[$productId]['quantity'] = $quantity;
        }

        session()->put(self::SESSION_KEY, $cart);

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
        $cart = $this->getCart();
        $productId = (int) $product->id;

        unset($cart[$productId]);
        session()->put(self::SESSION_KEY, $cart);

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
     * Retrieve cart array from session.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getCart(): array
    {
        return (array) session()->get(self::SESSION_KEY, []);
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
