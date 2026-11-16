<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display authenticated user's order list.
     */
    public function index(Request $request): View|RedirectResponse
    {
        if (! Auth::check()) {
            session()->flash('warning', 'Silakan login terlebih dahulu untuk melihat daftar pesanan!');

            return redirect()->guest(route('ecommerce.login'));
        }

        $status = $request->query('status');

        $orders = Order::query()
            ->where('user_id', (int) Auth::id())
            ->when($status, fn ($query) => $query->where('status', $status))
            ->with(['items.product', 'shippingMethod', 'paymentMethod'])
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('ecommerce.pages.order-list', compact('orders', 'status'));
    }

    /**
     * Upload payment proof for a user's order.
     */
    public function uploadPaymentProof(Request $request, string $orderNumber): RedirectResponse
    {
        if (! Auth::check()) {
            session()->flash('warning', 'Silakan login terlebih dahulu!');

            return redirect()->guest(route('ecommerce.login'));
        }

        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->where('user_id', (int) Auth::id())
            ->firstOrFail();

        $request->validate([
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
        ], [
            'payment_proof.required' => 'Pilih file bukti pembayaran terlebih dahulu.',
            'payment_proof.mimes' => 'Format file harus JPG, PNG, WebP, atau PDF.',
            'payment_proof.max' => 'Ukuran file maksimal 4MB.',
        ]);

        if ($order->payment_proof && Storage::disk('public')->exists($order->payment_proof)) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'payment_proof_uploaded_at' => now(),
            'payment_status' => in_array($order->payment_status, ['unpaid', 'pending'], true) ? 'waiting_verification' : $order->payment_status,
        ]);

        return redirect()
            ->route('ecommerce.checkout.detail', $order->order_number)
            ->with('success', __('Payment proof uploaded successfully and is awaiting verification.'));
    }
}
