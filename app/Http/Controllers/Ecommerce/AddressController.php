<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AddressController extends Controller
{
    /**
     * Display a listing of user addresses.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        if (! Auth::check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'unauthenticated',
                    'message' => __('Please login first to access your address list!'),
                    'redirect' => route('ecommerce.login'),
                ], 401);
            }

            session()->flash('warning', 'Silakan login terlebih dahulu untuk mengakses daftar alamat!');

            return redirect()->guest(route('ecommerce.login'));
        }

        $userId = (int) Auth::id();
        $addresses = UserAddress::query()
            ->where('user_id', $userId)
            ->orderByDesc('is_default')
            ->latest('id')
            ->get();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $addresses,
            ]);
        }

        return view('ecommerce.pages.address-list', [
            'addresses' => $addresses,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Store a newly created user address.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        if (! Auth::check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->guest(route('ecommerce.login'));
        }

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:25'],
            'address_line' => ['required', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $userId = (int) Auth::id();
        $addressCount = UserAddress::query()->where('user_id', $userId)->count();
        $isDefault = $request->boolean('is_default') || $addressCount === 0;

        if ($isDefault) {
            UserAddress::query()
                ->where('user_id', $userId)
                ->update(['is_default' => false]);
        }

        $address = UserAddress::create([
            'user_id' => $userId,
            'label' => $validated['label'],
            'recipient_name' => $validated['recipient_name'],
            'phone_number' => $validated['phone_number'],
            'address_line' => $validated['address_line'],
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_default' => $isDefault,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => __('Address added successfully!'),
                'data' => $address,
            ]);
        }

        session()->flash('success', 'Alamat berhasil ditambahkan!');

        return redirect()->route('ecommerce.address-list');
    }

    /**
     * Update the specified user address.
     */
    public function update(Request $request, UserAddress $address): RedirectResponse|JsonResponse
    {
        if (! Auth::check() || $address->user_id !== (int) Auth::id()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            abort(403);
        }

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:25'],
            'address_line' => ['required', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $userId = (int) Auth::id();
        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            UserAddress::query()
                ->where('user_id', $userId)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update([
            'label' => $validated['label'],
            'recipient_name' => $validated['recipient_name'],
            'phone_number' => $validated['phone_number'],
            'address_line' => $validated['address_line'],
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_default' => $isDefault ? true : $address->is_default,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => __('Address updated successfully!'),
                'data' => $address,
            ]);
        }

        session()->flash('success', 'Alamat berhasil diperbarui!');

        return redirect()->route('ecommerce.address-list');
    }

    /**
     * Remove the specified user address.
     */
    public function destroy(Request $request, UserAddress $address): RedirectResponse|JsonResponse
    {
        if (! Auth::check() || $address->user_id !== (int) Auth::id()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            abort(403);
        }

        $userId = (int) Auth::id();
        $wasDefault = (bool) $address->is_default;

        $address->delete();

        // If the deleted address was default, promote another address to default
        if ($wasDefault) {
            $nextAddress = UserAddress::query()
                ->where('user_id', $userId)
                ->latest('id')
                ->first();

            $nextAddress?->update(['is_default' => true]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => __('Address deleted successfully!'),
            ]);
        }

        session()->flash('success', 'Alamat berhasil dihapus!');

        return redirect()->route('ecommerce.address-list');
    }

    /**
     * Toggle or set the address as default.
     */
    public function toggleDefault(Request $request, UserAddress $address): RedirectResponse|JsonResponse
    {
        if (! Auth::check() || $address->user_id !== (int) Auth::id()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }

            abort(403);
        }

        $address->makeDefault();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => __('Primary address updated successfully!'),
                'data' => $address->fresh(),
            ]);
        }

        session()->flash('success', 'Alamat utama berhasil diperbarui!');

        return redirect()->route('ecommerce.address-list');
    }
}
