<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Menu;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class MenuController extends Controller
{
    /**
     * Display a listing of the menus.
     */
    public function index(Request $request): View
    {
        Gate::authorize('menus.view');

        $selectedApp = trim((string) $request->query('app', ''));

        $rootMenus = Menu::query()
            ->whereNull('parent_id')
            ->when($selectedApp !== '', fn ($q) => $q->forApp($selectedApp))
            ->with([
                'children' => function ($query) use ($selectedApp): void {
                    $query->when($selectedApp !== '', fn ($q) => $q->forApp($selectedApp))
                        ->orderBy('order')
                        ->orderBy('id');
                },
            ])
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $apps = Menu::distinctApps();

        $permissions = Permission::query()
            ->orderBy('name')
            ->pluck('name');

        $availableRoutes = collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter(fn ($name) => $name && ! str_starts_with($name, 'ignition.') && ! str_ends_with($name, '.html'))
            ->unique()
            ->sort()
            ->values();

        return view('modules.menus.index', [
            'menus' => $rootMenus,
            'apps' => $apps,
            'selectedApp' => $selectedApp,
            'permissions' => $permissions,
            'availableRoutes' => $availableRoutes,
        ]);
    }

    /**
     * Show the form for creating a new menu.
     */
    public function create(): View
    {
        Gate::authorize('menus.create');

        $parentMenus = Menu::query()
            ->where('type', 'dropdown')
            ->orderBy('order')
            ->get();

        $permissions = Permission::query()
            ->orderBy('name')
            ->pluck('name');

        $availableRoutes = collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter(fn ($name) => $name && ! str_starts_with($name, 'ignition.') && ! str_ends_with($name, '.html'))
            ->unique()
            ->sort()
            ->values();

        $apps = Menu::distinctApps();

        return view('modules.menus.create', [
            'parentMenus' => $parentMenus,
            'permissions' => $permissions,
            'availableRoutes' => $availableRoutes,
            'apps' => $apps,
        ]);
    }

    /**
     * Store a newly created menu in storage.
     */
    public function store(StoreMenuRequest $request): RedirectResponse|JsonResponse
    {
        Gate::authorize('menus.create');

        $data = $request->validated();
        if (($data['order'] ?? 0) === 0) {
            $maxOrder = Menu::query()
                ->where('parent_id', $data['parent_id'] ?? null)
                ->max('order');
            $data['order'] = is_numeric($maxOrder) ? ((int) $maxOrder + 1) : 1;
        }

        $menu = Menu::query()->create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Menu created successfully.'),
                'menu' => $menu->fresh('children'),
            ], 201);
        }

        return redirect()
            ->route('menus.index')
            ->with('success', __('Menu created successfully.'));
    }

    /**
     * Duplicate the specified menu.
     */
    public function duplicate(Request $request, Menu $menu): RedirectResponse|JsonResponse
    {
        Gate::authorize('menus.create');

        $copy = Menu::query()->create([
            'app' => $menu->app ?? ['online-shop'],
            'parent_id' => $menu->parent_id,
            'name' => $menu->name.' (Copy)',
            'type' => $menu->type,
            'icon' => $menu->icon,
            'route' => $menu->route,
            'url' => $menu->url,
            'permission' => $menu->permission,
            'order' => $menu->order + 1,
            'is_active' => $menu->is_active,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Menu duplicated successfully.'),
                'menu' => $copy,
            ]);
        }

        return redirect()
            ->route('menus.index')
            ->with('success', __('Menu duplicated successfully.'));
    }

    /**
     * Display the specified menu.
     */
    public function show(Menu $menu): View
    {
        Gate::authorize('menus.view');

        $menu->load(['parent', 'children']);

        return view('modules.menus.show', [
            'menu' => $menu,
        ]);
    }

    /**
     * Show the form for editing the specified menu.
     */
    public function edit(Menu $menu): View
    {
        Gate::authorize('menus.edit');

        $parentMenus = Menu::query()
            ->where('type', 'dropdown')
            ->where('id', '!=', $menu->id)
            ->orderBy('order')
            ->get();

        $permissions = Permission::query()
            ->orderBy('name')
            ->pluck('name');

        $availableRoutes = collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter(fn ($name) => $name && ! str_starts_with($name, 'ignition.') && ! str_ends_with($name, '.html'))
            ->unique()
            ->sort()
            ->values();

        $apps = Menu::distinctApps();

        return view('modules.menus.edit', [
            'menu' => $menu,
            'parentMenus' => $parentMenus,
            'permissions' => $permissions,
            'availableRoutes' => $availableRoutes,
            'apps' => $apps,
        ]);
    }

    /**
     * Update the specified menu in storage.
     */
    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse|JsonResponse
    {
        Gate::authorize('menus.edit');

        $menu->update($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Menu updated successfully.'),
                'menu' => $menu->fresh('children'),
            ]);
        }

        return redirect()
            ->route('menus.index')
            ->with('success', __('Menu updated successfully.'));
    }

    /**
     * Reorder menus and update parent relationships via AJAX drag-and-drop.
     */
    public function reorder(Request $request): JsonResponse
    {
        Gate::authorize('menus.edit');

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:menus,id'],
            'items.*.parent_id' => ['nullable', 'integer', 'exists:menus,id'],
            'items.*.order' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['items'] as $item) {
                $parentId = $item['parent_id'] ?? null;
                if ($parentId === (int) $item['id']) {
                    $parentId = null;
                }

                $menu = Menu::query()->find($item['id']);
                if ($menu) {
                    $menu->update([
                        'parent_id' => $parentId,
                        'order' => (int) $item['order'],
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => __('Menu order updated successfully.'),
        ]);
    }

    /**
     * Toggle active status of the specified menu.
     */
    public function toggleActive(Request $request, Menu $menu): RedirectResponse|JsonResponse
    {
        Gate::authorize('menus.edit');

        $menu->update([
            'is_active' => ! $menu->is_active,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $menu->is_active,
                'message' => __('Menu status updated successfully.'),
            ]);
        }

        return redirect()
            ->route('menus.index')
            ->with('success', __('Menu status updated successfully.'));
    }

    /**
     * Remove the specified menu from storage.
     */
    public function destroy(Request $request, Menu $menu): RedirectResponse|JsonResponse
    {
        Gate::authorize('menus.delete');

        $menu->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Menu deleted successfully.'),
            ]);
        }

        return redirect()
            ->route('menus.index')
            ->with('success', __('Menu deleted successfully.'));
    }

    /**
     * Export menus to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('menus.view');

        $menus = Menu::query()
            ->with(['parent', 'children'])
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN id ELSE parent_id END, parent_id IS NOT NULL, `order` ASC')
            ->get();

        return Pdf::loadView('modules.menus.pdf', [
            'menus' => $menus,
        ])->setPaper('a4', 'landscape')->download('menus_'.date('Y-m-d_His').'.pdf');
    }
}
