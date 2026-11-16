<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\ProductCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Display the ecommerce checkout page.
     */
    public function index(Request $request): View|RedirectResponse
    {
        if (! Auth::check()) {
            session()->flash('warning', 'Silakan login terlebih dahulu untuk mengakses checkout!');

            return redirect()->guest(route('ecommerce.login'));
        }

        $userId = (int) Auth::id();
        $cart = $this->getUserCartArray($userId);

        if (empty($cart)) {
            session()->flash('warning', 'Keranjang belanja Anda masih kosong!');

            return redirect()->route('ecommerce.cart');
        }

        $subtotal = $this->calculateSubtotal($cart);
        $totalQuantity = $this->calculateTotalQuantity($cart);

        return view('ecommerce.pages.checkout', [
            'cartItems' => $cart,
            'subtotal' => $subtotal,
            'totalQuantity' => $totalQuantity,
            'user' => Auth::user(),
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
