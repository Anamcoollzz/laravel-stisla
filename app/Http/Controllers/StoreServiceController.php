<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStoreServiceRequest;
use App\Http\Requests\UpdateStoreServiceRequest;
use App\Models\StoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StoreServiceController extends Controller
{
    /**
     * Display a listing of store services.
     */
    public function index(): View
    {
        Gate::authorize('store-services.view');

        $services = StoreService::query()->orderBy('order')->latest('id')->get();

        $stats = [
            'total' => $services->count(),
            'active' => $services->where('is_active', true)->count(),
            'inactive' => $services->where('is_active', false)->count(),
        ];

        return view('modules.store-services.index', compact('services', 'stats'));
    }

    /**
     * Show the form for creating a new store service.
     */
    public function create(): View
    {
        Gate::authorize('store-services.create');

        return view('modules.store-services.create');
    }

    /**
     * Store a newly created store service in storage.
     */
    public function store(StoreStoreServiceRequest $request): RedirectResponse
    {
        Gate::authorize('store-services.create');

        $data = $request->validated();

        if ($request->hasFile('icon_image')) {
            $data['icon_image'] = $request->file('icon_image')->store('store-services', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['order'] = $data['order'] ?? (StoreService::max('order') + 1);

        StoreService::create($data);

        return redirect()->route('store-services.index')->with('success', __('Service created successfully.'));
    }

    /**
     * Display the specified store service.
     */
    public function show(StoreService $storeService): View
    {
        Gate::authorize('store-services.view');

        return view('modules.store-services.show', ['service' => $storeService]);
    }

    /**
     * Show the form for editing the specified store service.
     */
    public function edit(StoreService $storeService): View
    {
        Gate::authorize('store-services.edit');

        return view('modules.store-services.edit', ['service' => $storeService]);
    }

    /**
     * Update the specified store service in storage.
     */
    public function update(UpdateStoreServiceRequest $request, StoreService $storeService): RedirectResponse
    {
        Gate::authorize('store-services.edit');

        $data = $request->validated();

        if ($request->hasFile('icon_image')) {
            if ($storeService->icon_image && Storage::disk('public')->exists($storeService->icon_image)) {
                Storage::disk('public')->delete($storeService->icon_image);
            }
            $data['icon_image'] = $request->file('icon_image')->store('store-services', 'public');
        } elseif ($request->boolean('remove_icon_image')) {
            if ($storeService->icon_image && Storage::disk('public')->exists($storeService->icon_image)) {
                Storage::disk('public')->delete($storeService->icon_image);
            }
            $data['icon_image'] = null;
        }

        $data['is_active'] = $request->boolean('is_active');

        $storeService->update($data);

        return redirect()->route('store-services.index')->with('success', __('Service updated successfully.'));
    }

    /**
     * Remove the specified store service from storage.
     */
    public function destroy(StoreService $storeService): RedirectResponse
    {
        Gate::authorize('store-services.delete');

        if ($storeService->icon_image && Storage::disk('public')->exists($storeService->icon_image)) {
            Storage::disk('public')->delete($storeService->icon_image);
        }

        $storeService->delete();

        return redirect()->route('store-services.index')->with('success', __('Service deleted successfully.'));
    }
}

