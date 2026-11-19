<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = [
            'dashboard' => ['view'],
            'classes' => ['view', 'create', 'edit', 'delete'],
            'teachers' => ['view', 'create', 'edit', 'delete'],
            'students' => ['view', 'create', 'edit', 'delete'],
            'subjects' => ['view', 'create', 'edit', 'delete'],
            'spp-payments' => ['view', 'create', 'edit', 'delete'],
            'users' => ['view', 'create', 'edit', 'delete'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'permissions' => ['view', 'create', 'edit', 'delete'],
            'fields' => ['view', 'create', 'edit', 'delete'],
            'tickets' => ['view', 'create', 'edit', 'delete'],
            'menus' => ['view', 'create', 'edit', 'delete'],
            'product-categories' => ['view', 'create', 'edit', 'delete'],
            'products' => ['view', 'create', 'edit', 'delete'],
            'product-brands' => ['view', 'create', 'edit', 'delete'],
            'product-vendors' => ['view', 'create', 'edit', 'delete'],
            'product-sizes' => ['view', 'create', 'edit', 'delete'],
            'product-colors' => ['view', 'create', 'edit', 'delete'],
            'product-banners' => ['view', 'create', 'edit', 'delete'],
            'our-teams' => ['view', 'create', 'edit', 'delete'],
            'blogs' => ['view', 'create', 'edit', 'delete'],
            'blog-comments' => ['view', 'edit', 'delete'],
            'testimonials' => ['view', 'create', 'edit', 'delete'],
            'product-reviews' => ['view', 'edit', 'delete'],
            'store-services' => ['view', 'create', 'edit', 'delete'],
            'ecommerce-faqs' => ['view', 'create', 'edit', 'delete'],
            'orders' => ['view', 'edit', 'delete'],
            'footer-product-categories' => ['view', 'create', 'edit', 'delete'],
            'shipping-methods' => ['view', 'create', 'edit', 'delete'],
            'payment-methods' => ['view', 'create', 'edit', 'delete'],
            'payment-banks' => ['view', 'create', 'edit', 'delete'],
            'chat' => ['view', 'create', 'delete'],
            'backup' => ['view', 'create', 'download', 'delete'],
            'log-requests' => ['view', 'delete'],
            'laravel-logs' => ['view', 'delete'],
            'database-tables' => ['view'],
            'news-categories' => ['view', 'create', 'edit', 'delete'],
            'news-tags' => ['view', 'create', 'edit', 'delete'],
            'news-comments' => ['view', 'create', 'edit', 'delete'],
            'news' => ['view', 'create', 'edit', 'delete'],
            'lp-company-stats' => ['view', 'create', 'edit', 'delete'],
            'lp-services' => ['view', 'create', 'edit', 'delete'],
            'lp-why-choose-us' => ['view', 'create', 'edit', 'delete'],
            'lp-recruitment-steps' => ['view', 'create', 'edit', 'delete'],
            'lp-testimonials' => ['view', 'create', 'edit', 'delete'],
            'lp-faqs' => ['view', 'create', 'edit', 'delete'],
            'lp-workers' => ['view', 'create', 'edit', 'delete'],
            'lp-galleries' => ['view', 'create', 'edit', 'delete'],
            'lp-company-profile' => ['view', 'edit'],
            'lp-company-introduction' => ['view', 'edit'],
            'lp-milestones' => ['view', 'create', 'edit', 'delete'],
            'lp-vision-mission' => ['view', 'edit'],
            'lp-company-values' => ['view', 'create', 'edit', 'delete'],
            'lp-contact-inquiries' => ['view', 'create', 'edit', 'delete'],
            'settings' => ['view', 'edit'],
        ];

        $allPermissions = [];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissionName = "{$module}.{$action}";
                $permission = Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
                $allPermissions[] = $permission;
            }
        }

        // Create Admin role and assign all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($allPermissions);

        // Create Staff role with operational permissions
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staffRole->syncPermissions([
            'dashboard.view',
            'classes.view',
            'classes.create',
            'classes.edit',
            'teachers.view',
            'teachers.create',
            'teachers.edit',
            'students.view',
            'students.create',
            'students.edit',
            'subjects.view',
            'subjects.create',
            'subjects.edit',
            'spp-payments.view',
            'spp-payments.create',
            'spp-payments.edit',
            'tickets.view',
            'tickets.create',
            'tickets.edit',
            'product-categories.view',
            'products.view',
            'product-brands.view',
            'product-vendors.view',
            'product-sizes.view',
            'product-colors.view',
            'product-banners.view',
            'our-teams.view',
            'chat.view',
            'chat.create',
            'chat.delete',
        ]);

        // Create User / Viewer role
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userRole->syncPermissions([
            'dashboard.view',
            'classes.view',
            'teachers.view',
            'students.view',
            'subjects.view',
            'spp-payments.view',
            'tickets.view',
            'tickets.create',
            'product-categories.view',
            'products.view',
            'product-brands.view',
            'product-vendors.view',
            'product-sizes.view',
            'product-colors.view',
            'product-banners.view',
            'our-teams.view',
            'chat.view',
            'chat.create',
        ]);
    }
}
