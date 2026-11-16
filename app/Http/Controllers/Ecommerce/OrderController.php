<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}
