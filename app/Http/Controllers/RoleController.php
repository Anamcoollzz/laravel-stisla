<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('roles.view');

        $roles = Role::query()->withCount(['permissions', 'users'])->orderBy('name')->get();

        return view('modules.roles.index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('roles.create');

        $permissionsByModule = $this->getPermissionsGroupedByModule();

        return view('modules.roles.create', [
            'permissionsByModule' => $permissionsByModule,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        Gate::authorize('roles.create');

        $role = Role::create([
            'name' => $request->validated('name'),
            'guard_name' => 'web',
        ]);

        if ($request->filled('permissions')) {
            $role->syncPermissions($request->validated('permissions'));
        }

        return redirect()
            ->route('roles.index')
            ->with('success', __('Role created successfully.'));
    }

    /**
     * Duplicate the specified resource.
     */
    public function duplicate(Role $role): RedirectResponse
    {
        Gate::authorize('roles.create');

        $baseName = $role->name;
        $newName = $baseName.'-copy';
        $counter = 1;
        while (Role::where('name', $newName)->where('guard_name', 'web')->exists()) {
            $counter++;
            $newName = $baseName.'-copy-'.$counter;
        }

        $newRole = Role::create([
            'name' => $newName,
            'guard_name' => 'web',
        ]);

        $permissions = $role->permissions->pluck('name')->all();
        if (! empty($permissions)) {
            $newRole->syncPermissions($permissions);
        }

        return redirect()
            ->route('roles.index')
            ->with('success', __('Role duplicated successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): View
    {
        Gate::authorize('roles.view');

        $role->load(['permissions', 'users']);

        return view('modules.roles.show', [
            'role' => $role,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View
    {
        Gate::authorize('roles.edit');

        $permissionsByModule = $this->getPermissionsGroupedByModule();
        $rolePermissions = $role->permissions->pluck('name')->all();

        return view('modules.roles.edit', [
            'role' => $role,
            'permissionsByModule' => $permissionsByModule,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        Gate::authorize('roles.edit');

        $role->update([
            'name' => $request->validated('name'),
        ]);

        $role->syncPermissions($request->input('permissions', []));

        return redirect()
            ->route('roles.index')
            ->with('success', __('Role updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        Gate::authorize('roles.delete');

        if ($role->name === 'admin') {
            return redirect()
                ->route('roles.index')
                ->with('error', __('The admin role cannot be deleted.'));
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', __('Role deleted successfully.'));
    }

    /**
     * Export roles to PDF.
     */
    public function exportPdf(): Response
    {
        Gate::authorize('roles.view');

        $roles = Role::query()->withCount(['permissions', 'users'])->orderBy('name')->get();

        return Pdf::loadView('modules.roles.pdf', [
            'roles' => $roles,
        ])->setPaper('a4', 'portrait')->download('roles_'.date('Y-m-d_His').'.pdf');
    }

    /**
     * Helper to group permissions by module.
     *
     * @return Collection<string, Collection<int, Permission>>
     */
    protected function getPermissionsGroupedByModule(): Collection
    {
        return Permission::query()
            ->orderBy('name')
            ->get()
            ->groupBy(function (Permission $permission) {
                return explode('.', $permission->name)[0] ?? 'general';
            });
    }
}
