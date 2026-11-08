<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Dashboard Section
        Menu::firstOrCreate(
            ['name' => 'Dashboard', 'type' => 'header'],
            ['order' => 10, 'is_active' => true]
        );

        Menu::firstOrCreate(
            ['name' => 'Dashboard', 'type' => 'item', 'route' => 'dashboard'],
            [
                'icon' => 'fas fa-fire',
                'order' => 20,
                'is_active' => true,
            ]
        );

        // 2. Modules Section
        Menu::firstOrCreate(
            ['name' => 'Modules', 'type' => 'header'],
            ['order' => 30, 'is_active' => true]
        );

        $modulesDropdown = Menu::firstOrCreate(
            ['name' => 'Modules', 'type' => 'dropdown'],
            [
                'icon' => 'fas fa-plug',
                'order' => 40,
                'is_active' => true,
            ]
        );

        $moduleChildren = [
            ['name' => 'Classes', 'route' => 'classes.index', 'permission' => 'classes.view', 'order' => 1],
            ['name' => 'Teachers', 'route' => 'teachers.index', 'permission' => 'teachers.view', 'order' => 2],
            ['name' => 'Students', 'route' => 'students.index', 'permission' => 'students.view', 'order' => 3],
            ['name' => 'Subjects', 'route' => 'subjects.index', 'permission' => 'subjects.view', 'order' => 4],
            ['name' => 'Fields', 'route' => 'fields.index', 'permission' => 'fields.view', 'order' => 5],
            ['name' => 'SPP Payments', 'route' => 'spp-payments.index', 'permission' => 'spp-payments.view', 'order' => 6],
            ['name' => 'Tickets', 'route' => 'tickets.index', 'permission' => 'tickets.view', 'order' => 7],
            ['name' => 'Chat Box', 'route' => 'chat.index', 'permission' => 'chat.view', 'order' => 8],
        ];

        // Clean up previous product items under Modules dropdown if any
        Menu::where('parent_id', $modulesDropdown->id)
            ->whereIn('name', ['Product Categories', 'Products'])
            ->delete();

        foreach ($moduleChildren as $child) {
            Menu::firstOrCreate(
                ['parent_id' => $modulesDropdown->id, 'name' => $child['name']],
                [
                    'type' => 'item',
                    'route' => $child['route'],
                    'permission' => $child['permission'],
                    'order' => $child['order'],
                    'is_active' => true,
                ]
            );
        }

        // Dedicated Products Dropdown
        $shopDropdown = Menu::firstOrCreate(
            ['name' => 'Shop / Products', 'type' => 'dropdown'],
            [
                'icon' => 'fas fa-shopping-bag',
                'order' => 45,
                'is_active' => true,
            ]
        );

        $shopChildren = [
            ['name' => 'Products', 'route' => 'products.index', 'permission' => 'products.view', 'order' => 1],
            ['name' => 'Categories', 'route' => 'product-categories.index', 'permission' => 'product-categories.view', 'order' => 2],
            ['name' => 'Brands', 'route' => 'product-brands.index', 'permission' => 'product-brands.view', 'order' => 3],
            ['name' => 'Vendors', 'route' => 'product-vendors.index', 'permission' => 'product-vendors.view', 'order' => 4],
            ['name' => 'Sizes', 'route' => 'product-sizes.index', 'permission' => 'product-sizes.view', 'order' => 5],
            ['name' => 'Colors', 'route' => 'product-colors.index', 'permission' => 'product-colors.view', 'order' => 6],
            ['name' => 'Banners', 'route' => 'product-banners.index', 'permission' => 'product-banners.view', 'order' => 7],
        ];

        foreach ($shopChildren as $child) {
            Menu::firstOrCreate(
                ['parent_id' => $shopDropdown->id, 'name' => $child['name']],
                [
                    'type' => 'item',
                    'route' => $child['route'],
                    'permission' => $child['permission'],
                    'order' => $child['order'],
                    'is_active' => true,
                ]
            );
        }

        // 3. User Management Section
        Menu::firstOrCreate(
            ['name' => 'User Management', 'type' => 'header'],
            ['order' => 50, 'is_active' => true]
        );

        $accessControlDropdown = Menu::firstOrCreate(
            ['name' => 'Access Control', 'type' => 'dropdown'],
            [
                'icon' => 'fas fa-users-cog',
                'order' => 60,
                'is_active' => true,
            ]
        );

        $accessChildren = [
            ['name' => 'Users', 'route' => 'users.index', 'permission' => 'users.view', 'order' => 1],
            ['name' => 'Roles', 'route' => 'roles.index', 'permission' => 'roles.view', 'order' => 2],
            ['name' => 'Permissions', 'route' => 'permissions.index', 'permission' => 'permissions.view', 'order' => 3],
            ['name' => 'Menu Management', 'route' => 'menus.index', 'permission' => 'menus.view', 'order' => 4],
            ['name' => 'Database Backup', 'route' => 'backups.index', 'permission' => 'backup.view', 'order' => 5],
        ];

        foreach ($accessChildren as $child) {
            Menu::firstOrCreate(
                ['parent_id' => $accessControlDropdown->id, 'name' => $child['name']],
                [
                    'type' => 'item',
                    'route' => $child['route'],
                    'permission' => $child['permission'],
                    'order' => $child['order'],
                    'is_active' => true,
                ]
            );
        }

        // 4. Settings Section
        Menu::firstOrCreate(
            ['name' => 'Settings', 'type' => 'header'],
            ['order' => 70, 'is_active' => true]
        );

        Menu::firstOrCreate(
            ['name' => 'Settings', 'type' => 'item', 'route' => 'settings.index'],
            [
                'icon' => 'fas fa-cog',
                'permission' => 'settings.view',
                'order' => 80,
                'is_active' => true,
            ]
        );
    }
}
