<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of payment methods.
     */
    public function index(): View
    {
        if (Gate::has('payment-methods.view')) {
            Gate::authorize('payment-methods.view');
        }

        $paymentMethods = PaymentMethod::query()->orderBy('order')->latest('id')->get();

        $stats = [
            'total' => $paymentMethods->count(),
            'active' => $paymentMethods->where('is_active', true)->count(),
            'inactive' => $paymentMethods->where('is_active', false)->count(),
        ];

        return view('modules.payment-methods.index', compact('paymentMethods', 'stats'));
    }

    /**
     * Show the form for creating a new payment method.
     */
    public function create(): View
    {
        if (Gate::has('payment-methods.create')) {
            Gate::authorize('payment-methods.create');
        }

        return view('modules.payment-methods.create');
    }

    /**
     * Store a newly created payment method in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (Gate::has('payment-methods.create')) {
            Gate::authorize('payment-methods.create');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'string', 'max:50'],
            'fee' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('payment-methods', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['order'] = $validated['order'] ?? (PaymentMethod::max('order') + 1);

        PaymentMethod::create($validated);

        return redirect()->route('payment-methods.index')->with('success', __('Metode pembayaran berhasil ditambahkan.'));
    }

    /**
     * Show the form for editing the specified payment method.
     */
    public function edit(PaymentMethod $paymentMethod): View
    {
        if (Gate::has('payment-methods.edit')) {
            Gate::authorize('payment-methods.edit');
        }

        return view('modules.payment-methods.edit', compact('paymentMethod'));
    }

    /**
     * Update the specified payment method in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        if (Gate::has('payment-methods.edit')) {
            Gate::authorize('payment-methods.edit');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'string', 'max:50'],
            'fee' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('logo')) {
            if ($paymentMethod->logo && ! str_starts_with($paymentMethod->logo, 'http')) {
                Storage::disk('public')->delete($paymentMethod->logo);
            }
            $validated['logo'] = $request->file('logo')->store('payment-methods', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['order'] = $validated['order'] ?? $paymentMethod->order;

        $paymentMethod->update($validated);

        return redirect()->route('payment-methods.index')->with('success', __('Metode pembayaran berhasil diperbarui.'));
    }

    /**
     * Remove the specified payment method from storage.
     */
    public function destroy(PaymentMethod $paymentMethod): RedirectResponse
    {
        if (Gate::has('payment-methods.delete')) {
            Gate::authorize('payment-methods.delete');
        }

        if ($paymentMethod->logo && ! str_starts_with($paymentMethod->logo, 'http')) {
            Storage::disk('public')->delete($paymentMethod->logo);
        }

        $paymentMethod->delete();

        return redirect()->route('payment-methods.index')->with('success', __('Metode pembayaran berhasil dihapus.'));
    }

    /**
     * Toggle active status.
     */
    public function toggle(PaymentMethod $paymentMethod): RedirectResponse
    {
        if (Gate::has('payment-methods.edit')) {
            Gate::authorize('payment-methods.edit');
        }

        $paymentMethod->update([
            'is_active' => ! $paymentMethod->is_active,
        ]);

        return redirect()->route('payment-methods.index')->with('success', __('Status metode pembayaran berhasil diubah.'));
    }
}
