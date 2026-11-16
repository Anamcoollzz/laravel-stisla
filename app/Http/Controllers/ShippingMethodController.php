<?php

namespace App\Http\Controllers;

use App\Models\ShippingMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ShippingMethodController extends Controller
{
    /**
     * Display a listing of shipping methods.
     */
    public function index(): View
    {
        if (Gate::has('shipping-methods.view')) {
            Gate::authorize('shipping-methods.view');
        }

        $shippingMethods = ShippingMethod::query()->orderBy('order')->latest('id')->get();

        $stats = [
            'total' => $shippingMethods->count(),
            'active' => $shippingMethods->where('is_active', true)->count(),
            'inactive' => $shippingMethods->where('is_active', false)->count(),
        ];

        return view('modules.shipping-methods.index', compact('shippingMethods', 'stats'));
    }

    /**
     * Show the form for creating a new shipping method.
     */
    public function create(): View
    {
        if (Gate::has('shipping-methods.create')) {
            Gate::authorize('shipping-methods.create');
        }

        return view('modules.shipping-methods.create');
    }

    /**
     * Store a newly created shipping method in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (Gate::has('shipping-methods.create')) {
            Gate::authorize('shipping-methods.create');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50'],
            'cost' => ['required', 'numeric', 'min:0'],
            'estimated_delivery' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('shipping-methods', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['order'] = $validated['order'] ?? (ShippingMethod::max('order') + 1);

        ShippingMethod::create($validated);

        return redirect()->route('shipping-methods.index')->with('success', __('Metode pengiriman berhasil ditambahkan.'));
    }

    /**
     * Show the form for editing the specified shipping method.
     */
    public function edit(ShippingMethod $shippingMethod): View
    {
        if (Gate::has('shipping-methods.edit')) {
            Gate::authorize('shipping-methods.edit');
        }

        return view('modules.shipping-methods.edit', compact('shippingMethod'));
    }

    /**
     * Update the specified shipping method in storage.
     */
    public function update(Request $request, ShippingMethod $shippingMethod): RedirectResponse
    {
        if (Gate::has('shipping-methods.edit')) {
            Gate::authorize('shipping-methods.edit');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50'],
            'cost' => ['required', 'numeric', 'min:0'],
            'estimated_delivery' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('logo')) {
            if ($shippingMethod->logo && ! str_starts_with($shippingMethod->logo, 'http')) {
                Storage::disk('public')->delete($shippingMethod->logo);
            }
            $validated['logo'] = $request->file('logo')->store('shipping-methods', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['order'] = $validated['order'] ?? $shippingMethod->order;

        $shippingMethod->update($validated);

        return redirect()->route('shipping-methods.index')->with('success', __('Metode pengiriman berhasil diperbarui.'));
    }

    /**
     * Remove the specified shipping method from storage.
     */
    public function destroy(ShippingMethod $shippingMethod): RedirectResponse
    {
        if (Gate::has('shipping-methods.delete')) {
            Gate::authorize('shipping-methods.delete');
        }

        if ($shippingMethod->logo && ! str_starts_with($shippingMethod->logo, 'http')) {
            Storage::disk('public')->delete($shippingMethod->logo);
        }

        $shippingMethod->delete();

        return redirect()->route('shipping-methods.index')->with('success', __('Metode pengiriman berhasil dihapus.'));
    }

    /**
     * Toggle active status.
     */
    public function toggle(ShippingMethod $shippingMethod): RedirectResponse
    {
        if (Gate::has('shipping-methods.edit')) {
            Gate::authorize('shipping-methods.edit');
        }

        $shippingMethod->update([
            'is_active' => ! $shippingMethod->is_active,
        ]);

        return redirect()->route('shipping-methods.index')->with('success', __('Status metode pengiriman berhasil diubah.'));
    }
}
