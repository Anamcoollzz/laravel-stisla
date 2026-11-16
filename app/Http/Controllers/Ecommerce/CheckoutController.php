<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentBank;
use App\Models\PaymentMethod;
use App\Models\ProductCart;
use App\Models\ShippingMethod;
use App\Models\UserAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $addresses = UserAddress::query()
            ->where('user_id', $userId)
            ->orderByDesc('is_default')
            ->latest('id')
            ->get();
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();

        $shippingMethods = ShippingMethod::query()
            ->active()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $paymentMethods = PaymentMethod::query()
            ->active()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $paymentBanks = PaymentBank::query()
            ->active()
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('ecommerce.pages.checkout', [
            'cartItems' => $cart,
            'subtotal' => $subtotal,
            'totalQuantity' => $totalQuantity,
            'user' => Auth::user(),
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'shippingMethods' => $shippingMethods,
            'paymentMethods' => $paymentMethods,
            'paymentBanks' => $paymentBanks,
        ]);
    }

    /**
     * Process placing the order.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            session()->flash('warning', 'Silakan login terlebih dahulu untuk melakukan checkout!');

            return redirect()->guest(route('ecommerce.login'));
        }

        $userId = (int) Auth::id();
        $cart = $this->getUserCartArray($userId);

        if (empty($cart)) {
            session()->flash('warning', 'Keranjang belanja Anda kosong!');

            return redirect()->route('ecommerce.cart');
        }

        $validated = $request->validate([
            'address_id' => ['nullable', 'integer', 'exists:user_addresses,id'],
            'shipping_method_id' => ['required', 'integer', 'exists:shipping_methods,id'],
            'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
            'payment_bank_id' => ['nullable', 'integer', 'exists:payment_banks,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
        ]);

        // Resolve Address
        $address = null;
        if (! empty($validated['address_id'])) {
            $address = UserAddress::query()
                ->where('user_id', $userId)
                ->find($validated['address_id']);
        }
        if (! $address) {
            $address = UserAddress::query()
                ->where('user_id', $userId)
                ->where('is_default', true)
                ->first()
                ?? UserAddress::query()->where('user_id', $userId)->first();
        }

        if (! $address) {
            session()->flash('warning', 'Silakan tambahkan alamat pengiriman terlebih dahulu sebelum checkout!');

            return redirect()->route('ecommerce.address-list');
        }

        // Resolve Shipping Method
        $shipping = ShippingMethod::query()
            ->active()
            ->findOrFail($validated['shipping_method_id']);

        // Resolve Payment Method
        $payment = PaymentMethod::query()
            ->active()
            ->findOrFail($validated['payment_method_id']);

        // Resolve Payment Bank if bank transfer
        $bank = null;
        if ($payment->type === 'bank_transfer') {
            if (! empty($validated['payment_bank_id'])) {
                $bank = PaymentBank::query()->active()->find($validated['payment_bank_id']);
            }
            if (! $bank) {
                $bank = PaymentBank::query()->active()->orderBy('order')->first();
            }
        }

        $subtotal = $this->calculateSubtotal($cart);
        $shippingCost = (float) $shipping->cost;
        $paymentFee = (float) $payment->fee;
        $discountAmount = 0.0;
        $grandTotal = max(0.0, $subtotal + $shippingCost + $paymentFee - $discountAmount);

        $order = DB::transaction(function () use ($userId, $address, $shipping, $payment, $bank, $subtotal, $shippingCost, $paymentFee, $discountAmount, $grandTotal, $validated, $cart) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $userId,
                'user_address_id' => $address->id,
                'shipping_method_id' => $shipping->id,
                'payment_method_id' => $payment->id,
                'payment_bank_id' => $bank?->id,
                'recipient_name' => $address->recipient_name,
                'phone_number' => $address->phone_number,
                'address_line' => $address->address_line,
                'city' => $address->city,
                'state' => $address->state,
                'postal_code' => $address->postal_code,
                'shipping_name' => $shipping->name,
                'shipping_code' => $shipping->code,
                'shipping_cost' => $shippingCost,
                'shipping_estimated_delivery' => $shipping->estimated_delivery,
                'payment_name' => $payment->name,
                'payment_type' => $payment->type,
                'payment_fee' => $paymentFee,
                'bank_name' => $bank?->bank_name,
                'bank_account_number' => $bank?->account_number,
                'bank_account_name' => $bank?->account_name,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'coupon_code' => $validated['coupon_code'] ?? null,
                'grand_total' => $grandTotal,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($cart as $item) {
                $order->items()->create([
                    'product_id' => $item['id'],
                    'product_title' => $item['title'],
                    'product_slug' => $item['slug'],
                    'product_thumbnail' => $item['thumbnail'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            // Clear Cart after successful order placement
            ProductCart::query()->where('user_id', $userId)->delete();

            return $order;
        });

        session()->flash('success', 'Pesanan Anda (#'.$order->order_number.') berhasil dibuat!');

        return redirect()->route('ecommerce.checkout.detail', $order->order_number);
    }

    /**
     * Display checkout order details.
     */
    public function show(string $orderNumber): View|RedirectResponse
    {
        if (! Auth::check()) {
            session()->flash('warning', 'Silakan login terlebih dahulu!');

            return redirect()->guest(route('ecommerce.login'));
        }

        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'shippingMethod', 'paymentMethod', 'paymentBank'])
            ->firstOrFail();

        return view('ecommerce.pages.checkout-detail', [
            'order' => $order,
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
