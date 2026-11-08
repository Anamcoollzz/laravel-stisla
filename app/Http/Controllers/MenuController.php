<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class MenuController extends Controller
{
    /**
     * Display a listing of the menus.
     */
    public function index(): View
    {
        Gate::authorize('menus.view');

        $menus = Menu::query()
            ->with(['parent', 'children'])
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN id ELSE parent_id END, parent_id IS NOT NULL, `order` ASC')
            ->get();

        return view('modules.menus.index', [
            'menus' => $menus,
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

        return view('modules.menus.create', [
            'parentMenus' => $parentMenus,
            'permissions' => $permissions,
            'availableRoutes' => $availableRoutes,
        ]);
    }

    /**
     * Store a newly created menu in storage.
     */
    public function store(StoreMenuRequest $request): RedirectResponse
    {
        Gate::authorize('menus.create');

        Menu::query()->create($request->validated());

        return redirect()
            ->route('menus.index')
            ->with('success', __('Menu created successfully.'));
    }

    /**
     * Duplicate the specified menu.
     */
    public function duplicate(Menu $menu): RedirectResponse
    {
        Gate::authorize('menus.create');

        Menu::query()->create([
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

        return view('modules.menus.edit', [
            'menu' => $menu,
            'parentMenus' => $parentMenus,
            'permissions' => $permissions,
            'availableRoutes' => $availableRoutes,
        ]);
    }

    /**
     * Update the specified menu in storage.
     */
    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse
    {
        Gate::authorize('menus.edit');

        $menu->update($request->validated());

        return redirect()
            ->route('menus.index')
            ->with('success', __('Menu updated successfully.'));
    }

    /**
     * Remove the specified menu from storage.
     */
    public function destroy(Menu $menu): RedirectResponse
    {
        Gate::authorize('menus.delete');

        $menu->delete();

        return redirect()
            ->route('menus.index')
            ->with('success', __('Menu deleted successfully.'));
    }
}
