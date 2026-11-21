<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\ActivityLog;
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

        $permission = Permission::query()->create([
            'name' => $request->validated('name'),
            'guard_name' => 'web',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        ActivityLog::record(
            module: 'permissions',
            action: 'created',
            description: "Created permission {$permission->name}",
            subject: $permission,
            subjectLabel: $permission->name,
            before: null,
            after: [
                'name' => $permission->name,
                'guard_name' => $permission->guard_name,
            ],
        );

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

        $newPermission = Permission::query()->create([
            'name' => $newName,
            'guard_name' => 'web',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        ActivityLog::record(
            module: 'permissions',
            action: 'duplicated',
            description: "Duplicated permission {$permission->name} to {$newPermission->name}",
            subject: $newPermission,
            subjectLabel: $newPermission->name,
            before: ['source_permission' => $permission->name],
            after: ['name' => $newPermission->name],
        );

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

        $oldName = $permission->name;

        $permission->update([
            'name' => $request->validated('name'),
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        if ($oldName !== $permission->name) {
            ActivityLog::record(
                module: 'permissions',
                action: 'updated',
                description: "Updated permission {$oldName} to {$permission->name}",
                subject: $permission,
                subjectLabel: $permission->name,
                before: ['name' => $oldName],
                after: ['name' => $permission->name],
            );
        }

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

        ActivityLog::record(
            module: 'permissions',
            action: 'deleted',
            description: "Deleted permission {$permission->name}",
            subject: $permission,
            subjectLabel: $permission->name,
            before: [
                'name' => $permission->name,
                'guard_name' => $permission->guard_name,
            ],
            after: null,
        );

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
