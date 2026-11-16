<?php

namespace App\Http\Controllers;

use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with filters.
     */
    public function index(Request $request): View
    {
        Gate::authorize('orders.view');

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => trim((string) $request->query('status', '')),
            'payment_status' => trim((string) $request->query('payment_status', '')),
            'payment_type' => trim((string) $request->query('payment_type', '')),
            'has_proof' => trim((string) $request->query('has_proof', '')),
            'date_from' => trim((string) $request->query('date_from', '')),
            'date_to' => trim((string) $request->query('date_to', '')),
        ];

        $orders = Order::query()
            ->with(['user', 'items', 'shippingMethod', 'paymentMethod'])
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhere('recipient_name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->when($filters['status'] !== '', fn ($q) => $q->where('status', $filters['status']))
            ->when($filters['payment_status'] !== '', fn ($q) => $q->where('payment_status', $filters['payment_status']))
            ->when($filters['payment_type'] !== '', fn ($q) => $q->where('payment_type', $filters['payment_type']))
            ->when($filters['has_proof'] === '1', fn ($q) => $q->whereNotNull('payment_proof'))
            ->when($filters['has_proof'] === '0', fn ($q) => $q->whereNull('payment_proof'))
            ->when($filters['date_from'] !== '', fn ($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when($filters['date_to'] !== '', fn ($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
            ->latest('id')
            ->get();

        $allOrders = Order::query();
        $stats = [
            'total' => (clone $allOrders)->count(),
            'pending' => (clone $allOrders)->whereIn('status', ['pending', 'processing'])->count(),
            'completed' => (clone $allOrders)->where('status', 'completed')->count(),
            'waiting_verification' => (clone $allOrders)->where('payment_status', 'waiting_verification')->count(),
            'revenue' => (float) (clone $allOrders)->where('payment_status', 'paid')->sum('grand_total'),
        ];

        $paymentTypes = Order::query()->select('payment_type')->distinct()->pluck('payment_type')->filter()->values();

        return view('modules.orders.index', compact('orders', 'filters', 'stats', 'paymentTypes'));
    }

    /**
     * Display the specified order details.
     */
    public function show(Order $order): View
    {
        Gate::authorize('orders.view');

        $order->load(['user', 'address', 'shippingMethod', 'paymentMethod', 'paymentBank', 'items.product']);

        return view('modules.orders.show', compact('order'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('orders.edit');

        $validated = $request->validate([
            'status' => ['required', 'in:pending,processing,shipped,completed,cancelled'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        $order->update(['status' => $validated['status']]);

        if ($request->boolean('send_email')) {
            $this->notifyCustomer($order, 'order');
        }

        return redirect()->route('orders.show', $order)->with('success', __('Order status updated successfully.'));
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('orders.edit');

        $validated = $request->validate([
            'payment_status' => ['required', 'in:unpaid,waiting_verification,paid,failed,expired'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        $order->update(['payment_status' => $validated['payment_status']]);

        if ($request->boolean('send_email')) {
            $this->notifyCustomer($order, 'payment');
        }

        return redirect()->route('orders.show', $order)->with('success', __('Payment status updated successfully.'));
    }

    /**
     * Send order or payment status update notification email to the customer.
     */
    protected function notifyCustomer(Order $order, string $updateType): void
    {
        $order->loadMissing('user');
        $email = $order->user?->email;

        if (! $email) {
            return;
        }

        try {
            Mail::to($email)->send(new OrderStatusUpdatedMail($order, $updateType));
        } catch (Throwable $e) {
            Log::warning('Failed to send order status notification email.', [
                'order_id' => $order->id,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update shipping tracking number and receipt proof.
     */
    public function updateTracking(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('orders.edit');

        $validated = $request->validate([
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'tracking_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
            'remove_tracking_proof' => ['nullable', 'boolean'],
        ]);

        $data = [
            'tracking_number' => $validated['tracking_number'] ?? null,
        ];

        if ($request->hasFile('tracking_proof')) {
            if ($order->tracking_proof && Storage::disk('public')->exists($order->tracking_proof)) {
                Storage::disk('public')->delete($order->tracking_proof);
            }
            $data['tracking_proof'] = $request->file('tracking_proof')->store('tracking-proofs', 'public');
        } elseif ($request->boolean('remove_tracking_proof')) {
            if ($order->tracking_proof && Storage::disk('public')->exists($order->tracking_proof)) {
                Storage::disk('public')->delete($order->tracking_proof);
            }
            $data['tracking_proof'] = null;
        }

        $order->update($data);

        return redirect()->route('orders.show', $order)->with('success', __('Shipping receipt updated successfully.'));
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order): RedirectResponse
    {
        Gate::authorize('orders.delete');

        if ($order->payment_proof && Storage::disk('public')->exists($order->payment_proof)) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        if ($order->tracking_proof && Storage::disk('public')->exists($order->tracking_proof)) {
            Storage::disk('public')->delete($order->tracking_proof);
        }

        $order->delete();

        return redirect()->route('orders.index')->with('success', __('Order deleted successfully.'));
    }
}
