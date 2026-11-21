<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Menu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stisla:sync-permissions
                            {--prune : Delete permissions in database that are not defined in config or menus}
                            {--fresh-roles : Reset default roles (staff, user) to their config default permissions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan module definitions in config/stisla.php and sync permissions to database without overwriting existing roles';

    /**
     * Execute the console command.
     */
    public function handle(PermissionRegistrar $permissionRegistrar): int
    {
        $permissionRegistrar->forgetCachedPermissions();

        $guard = (string) config('stisla.permissions.guard', 'web');
        $superAdminRoleName = (string) config('stisla.permissions.super_admin_role', 'admin');
        $modules = (array) config('stisla.permissions.modules', []);
        $defaultRoles = (array) config('stisla.permissions.default_roles', []);

        $configuredPermissions = [];
        foreach ($modules as $module => $actions) {
            foreach ((array) $actions as $action) {
                $configuredPermissions[] = "{$module}.{$action}";
            }
        }

        if (Schema::hasTable('menus')) {
            $menuPermissions = Menu::query()
                ->whereNotNull('permission')
                ->where('permission', '!=', '')
                ->pluck('permission')
                ->map(fn (mixed $perm): string => trim((string) $perm))
                ->filter()
                ->all();

            $configuredPermissions = array_values(array_unique(array_merge($configuredPermissions, $menuPermissions)));
        } else {
            $configuredPermissions = array_values(array_unique($configuredPermissions));
        }

        $existingPermissions = Permission::query()
            ->where('guard_name', $guard)
            ->pluck('name')
            ->all();

        $createdPermissions = [];
        foreach ($configuredPermissions as $permissionName) {
            $permission = Permission::query()->firstOrCreate([
                'name' => $permissionName,
                'guard_name' => $guard,
            ]);

            if ($permission->wasRecentlyCreated) {
                $createdPermissions[] = $permissionName;
            }
        }

        $prunedPermissions = [];
        if ($this->option('prune')) {
            $toPrune = array_values(array_diff($existingPermissions, $configuredPermissions));
            if (! empty($toPrune)) {
                Permission::query()
                    ->where('guard_name', $guard)
                    ->whereIn('name', $toPrune)
                    ->delete();
                $prunedPermissions = $toPrune;
            }
        }

        // Super admin role always gets all configured permissions additively (preserving any custom permissions unless pruned)
        $superAdminRole = Role::query()->firstOrCreate([
            'name' => $superAdminRoleName,
            'guard_name' => $guard,
        ]);
        $superAdminRole->givePermissionTo($configuredPermissions);

        // Other default roles: only seed permissions if the role is newly created or --fresh-roles is passed
        $rolesCreated = $superAdminRole->wasRecentlyCreated ? [$superAdminRoleName] : [];
        $rolesPreserved = [];
        $rolesReset = [];

        foreach ($defaultRoles as $roleName => $rolePermissions) {
            $role = Role::query()->firstOrCreate([
                'name' => (string) $roleName,
                'guard_name' => $guard,
            ]);

            $validRolePermissions = array_values(array_intersect((array) $rolePermissions, $configuredPermissions));

            if ($role->wasRecentlyCreated) {
                $role->syncPermissions($validRolePermissions);
                $rolesCreated[] = (string) $roleName;
            } elseif ($this->option('fresh-roles')) {
                $role->syncPermissions($validRolePermissions);
                $rolesReset[] = (string) $roleName;
            } else {
                $rolesPreserved[] = (string) $roleName;
            }
        }

        $permissionRegistrar->forgetCachedPermissions();

        if ((! empty($createdPermissions) || ! empty($prunedPermissions)) && Schema::hasTable('activity_logs')) {
            ActivityLog::record(
                module: 'permissions',
                action: 'synced',
                description: sprintf(
                    'Synced permissions from config (%d created, %d pruned)',
                    count($createdPermissions),
                    count($prunedPermissions)
                ),
                subjectLabel: 'config/stisla.php',
                before: [
                    'total_count' => count($existingPermissions),
                    'pruned' => $prunedPermissions,
                ],
                after: [
                    'total_count' => Permission::query()->where('guard_name', $guard)->count(),
                    'created' => $createdPermissions,
                ],
            );
        }

        $this->components->info('Permissions synchronized successfully.');
        $this->table(
            ['Metric', 'Count / Value'],
            [
                ['Modules scanned', (string) count($modules)],
                ['Total permissions in config', (string) count($configuredPermissions)],
                ['New permissions created', (string) count($createdPermissions)],
                ['Orphan permissions pruned', (string) count($prunedPermissions)],
                ['Super Admin role updated', $superAdminRoleName],
                ['Newly created default roles', empty($rolesCreated) ? '-' : implode(', ', $rolesCreated)],
                ['Existing roles preserved (untouched)', empty($rolesPreserved) ? '-' : implode(', ', $rolesPreserved)],
                ['Existing roles reset (--fresh-roles)', empty($rolesReset) ? '-' : implode(', ', $rolesReset)],
            ]
        );

        return self::SUCCESS;
    }
}
