<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('permissions.view');

        $permissions = Permission::query()->withCount('roles')->orderBy('name')->get();

        return view('modules.permissions.index', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('permissions.create');

        return view('modules.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request): RedirectResponse
    {
        Gate::authorize('permissions.create');

        Permission::query()->create([
            'name' => $request->validated('name'),
            'guard_name' => 'web',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('permissions.index')
            ->with('success', __('Permission created successfully.'));
    }

    /**
     * Duplicate the specified resource.
     */
    public function duplicate(Permission $permission): RedirectResponse
    {
        Gate::authorize('permissions.create');

        $baseName = $permission->name;
        $newName = $baseName.'-copy';
        $counter = 1;
        while (Permission::where('name', $newName)->where('guard_name', 'web')->exists()) {
            $counter++;
            $newName = $baseName.'-copy-'.$counter;
        }

        Permission::query()->create([
            'name' => $newName,
            'guard_name' => 'web',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('permissions.index')
            ->with('success', __('Permission duplicated successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission): View
    {
        Gate::authorize('permissions.edit');

        return view('modules.permissions.edit', [
            'permission' => $permission,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        Gate::authorize('permissions.edit');

        $permission->update([
            'name' => $request->validated('name'),
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('permissions.index')
            ->with('success', __('Permission updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        Gate::authorize('permissions.delete');

        $permission->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('permissions.index')
            ->with('success', __('Permission deleted successfully.'));
    }

    /**
     * Export permissions to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('permissions.view');

        $permissions = Permission::query()->withCount('roles')->orderBy('name')->get();

        return Pdf::loadView('modules.permissions.pdf', [
            'permissions' => $permissions,
        ])->setPaper('a4', 'portrait')->download('permissions_'.date('Y-m-d_His').'.pdf');
    }
}
