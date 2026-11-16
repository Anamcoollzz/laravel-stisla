<?php

namespace App\Http\Controllers;

use App\Models\PaymentBank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentBankController extends Controller
{
    /**
     * Display a listing of payment banks.
     */
    public function index(): View
    {
        if (Gate::has('payment-banks.view')) {
            Gate::authorize('payment-banks.view');
        }

        $banks = PaymentBank::query()->orderBy('order')->latest('id')->get();

        $stats = [
            'total' => $banks->count(),
            'active' => $banks->where('is_active', true)->count(),
            'inactive' => $banks->where('is_active', false)->count(),
        ];

        return view('modules.payment-banks.index', compact('banks', 'stats'));
    }

    /**
     * Show the form for creating a new bank.
     */
    public function create(): View
    {
        if (Gate::has('payment-banks.create')) {
            Gate::authorize('payment-banks.create');
        }

        return view('modules.payment-banks.create');
    }

    /**
     * Store a newly created bank in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (Gate::has('payment-banks.create')) {
            Gate::authorize('payment-banks.create');
        }

        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_code' => ['nullable', 'string', 'max:20'],
            'account_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'branch' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'qr_code_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('payment-banks', 'public');
        }

        if ($request->hasFile('qr_code_image')) {
            $validated['qr_code_image'] = $request->file('qr_code_image')->store('payment-banks/qr', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['order'] = $validated['order'] ?? (PaymentBank::max('order') + 1);

        PaymentBank::create($validated);

        return redirect()->route('payment-banks.index')->with('success', __('Akun bank pembayaran berhasil ditambahkan.'));
    }

    /**
     * Show the form for editing the specified bank.
     */
    public function edit(PaymentBank $paymentBank): View
    {
        if (Gate::has('payment-banks.edit')) {
            Gate::authorize('payment-banks.edit');
        }

        return view('modules.payment-banks.edit', compact('paymentBank'));
    }

    /**
     * Update the specified bank in storage.
     */
    public function update(Request $request, PaymentBank $paymentBank): RedirectResponse
    {
        if (Gate::has('payment-banks.edit')) {
            Gate::authorize('payment-banks.edit');
        }

        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_code' => ['nullable', 'string', 'max:20'],
            'account_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'branch' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'qr_code_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('logo')) {
            if ($paymentBank->logo && ! str_starts_with($paymentBank->logo, 'http')) {
                Storage::disk('public')->delete($paymentBank->logo);
            }
            $validated['logo'] = $request->file('logo')->store('payment-banks', 'public');
        }

        if ($request->hasFile('qr_code_image')) {
            if ($paymentBank->qr_code_image && ! str_starts_with($paymentBank->qr_code_image, 'http')) {
                Storage::disk('public')->delete($paymentBank->qr_code_image);
            }
            $validated['qr_code_image'] = $request->file('qr_code_image')->store('payment-banks/qr', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['order'] = $validated['order'] ?? $paymentBank->order;

        $paymentBank->update($validated);

        return redirect()->route('payment-banks.index')->with('success', __('Akun bank pembayaran berhasil diperbarui.'));
    }

    /**
     * Remove the specified bank from storage.
     */
    public function destroy(PaymentBank $paymentBank): RedirectResponse
    {
        if (Gate::has('payment-banks.delete')) {
            Gate::authorize('payment-banks.delete');
        }

        if ($paymentBank->logo && ! str_starts_with($paymentBank->logo, 'http')) {
            Storage::disk('public')->delete($paymentBank->logo);
        }

        if ($paymentBank->qr_code_image && ! str_starts_with($paymentBank->qr_code_image, 'http')) {
            Storage::disk('public')->delete($paymentBank->qr_code_image);
        }

        $paymentBank->delete();

        return redirect()->route('payment-banks.index')->with('success', __('Akun bank pembayaran berhasil dihapus.'));
    }

    /**
     * Toggle active status.
     */
    public function toggle(PaymentBank $paymentBank): RedirectResponse
    {
        if (Gate::has('payment-banks.edit')) {
            Gate::authorize('payment-banks.edit');
        }

        $paymentBank->update([
            'is_active' => ! $paymentBank->is_active,
        ]);

        return redirect()->route('payment-banks.index')->with('success', __('Status akun bank pembayaran berhasil diubah.'));
    }
}
