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

        Menu::firstOrCreate(
            ['name' => 'Ecommerce Dashboard', 'type' => 'item', 'route' => 'dashboard.ecommerce'],
            [
                'icon' => 'fas fa-shopping-cart',
                'order' => 25,
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
            ['name' => 'Our Team', 'route' => 'our-teams.index', 'permission' => 'our-teams.view', 'order' => 8],
            ['name' => 'Blogs', 'route' => 'blogs.index', 'permission' => 'blogs.view', 'order' => 9],
            ['name' => 'Blog Comments', 'route' => 'blog-comments.index', 'permission' => 'blog-comments.view', 'order' => 10],
            ['name' => 'Testimonials', 'route' => 'testimonials.index', 'permission' => 'testimonials.view', 'order' => 11],
            ['name' => 'Product Reviews', 'route' => 'product-reviews.index', 'permission' => 'product-reviews.view', 'order' => 12],
            ['name' => 'Services', 'route' => 'store-services.index', 'permission' => 'store-services.view', 'order' => 13],
            ['name' => 'FAQs', 'route' => 'ecommerce-faqs.index', 'permission' => 'ecommerce-faqs.view', 'order' => 14],
            ['name' => 'Orders', 'route' => 'orders.index', 'permission' => 'orders.view', 'order' => 15],
            ['name' => 'Footer Categories', 'route' => 'footer-product-categories.index', 'permission' => 'footer-product-categories.view', 'order' => 16],
            ['name' => 'Shipping Methods', 'route' => 'shipping-methods.index', 'permission' => 'shipping-methods.view', 'order' => 15],
            ['name' => 'Payment Methods', 'route' => 'payment-methods.index', 'permission' => 'payment-methods.view', 'order' => 16],
            ['name' => 'Payment Banks', 'route' => 'payment-banks.index', 'permission' => 'payment-banks.view', 'order' => 17],
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

        $allApps = ['online-shop', 'news', 'lp-compro-workers'];

        // 3. User Management Section
        $oldAccessControlDropdown = Menu::query()
            ->where('name', 'Access Control')
            ->where('type', 'dropdown')
            ->first();

        if ($oldAccessControlDropdown) {
            Menu::query()->where('parent_id', $oldAccessControlDropdown->id)->delete();
            $oldAccessControlDropdown->delete();
        }

        Menu::updateOrCreate(
            ['name' => 'User Management', 'type' => 'header'],
            ['app' => $allApps, 'order' => 50, 'is_active' => true]
        );

        $userManagementItems = [
            ['name' => 'Users', 'icon' => 'fas fa-users', 'route' => 'users.index', 'permission' => 'users.view', 'order' => 51],
            ['name' => 'Roles', 'icon' => 'fas fa-user-tag', 'route' => 'roles.index', 'permission' => 'roles.view', 'order' => 52],
            ['name' => 'Permissions', 'icon' => 'fas fa-key', 'route' => 'permissions.index', 'permission' => 'permissions.view', 'order' => 53],
            ['name' => 'Menu Management', 'icon' => 'fas fa-bars', 'route' => 'menus.index', 'permission' => 'menus.view', 'order' => 54],
        ];

        foreach ($userManagementItems as $item) {
            Menu::updateOrCreate(
                ['name' => $item['name'], 'type' => 'item', 'route' => $item['route']],
                [
                    'app' => $allApps,
                    'parent_id' => null,
                    'icon' => $item['icon'],
                    'permission' => $item['permission'],
                    'order' => $item['order'],
                    'is_active' => true,
                ]
            );
        }

        // System & Logs Section
        Menu::updateOrCreate(
            ['name' => 'System & Logs', 'type' => 'header'],
            ['app' => $allApps, 'order' => 55, 'is_active' => true]
        );

        $systemItems = [
            ['name' => 'Database Backup', 'icon' => 'fas fa-database', 'route' => 'backups.index', 'permission' => 'backup.view', 'order' => 56],
            ['name' => 'Database Tables', 'icon' => 'fas fa-table', 'route' => 'database-tables.index', 'permission' => 'database-tables.view', 'order' => 57],
            ['name' => 'Log Request', 'icon' => 'fas fa-history', 'route' => 'log-requests.index', 'permission' => 'log-requests.view', 'order' => 58],
            ['name' => 'Laravel Logs', 'icon' => 'fas fa-file-alt', 'route' => 'laravel-logs.index', 'permission' => 'laravel-logs.view', 'order' => 59],
        ];

        foreach ($systemItems as $item) {
            Menu::updateOrCreate(
                ['name' => $item['name'], 'type' => 'item', 'route' => $item['route']],
                [
                    'app' => $allApps,
                    'parent_id' => null,
                    'icon' => $item['icon'],
                    'permission' => $item['permission'],
                    'order' => $item['order'],
                    'is_active' => true,
                ]
            );
        }

        // 4. Settings Section
        Menu::updateOrCreate(
            ['name' => 'Settings', 'type' => 'header'],
            ['app' => $allApps, 'order' => 70, 'is_active' => true]
        );

        Menu::updateOrCreate(
            ['name' => 'Settings', 'type' => 'item', 'route' => 'settings.index'],
            [
                'app' => $allApps,
                'icon' => 'fas fa-cog',
                'permission' => 'settings.view',
                'order' => 80,
                'is_active' => true,
            ]
        );

        // 5. News App Menus (ADMIN_MENU_APP=news)
        $this->firstOrCreateForApp('news', ['name' => 'Dashboard', 'type' => 'item'], [
            'icon' => 'fas fa-fire',
            'route' => 'dashboard',
            'order' => 1,
            'is_active' => true,
        ]);

        $newsDropdown = $this->firstOrCreateForApp('news', ['name' => 'News Management', 'type' => 'dropdown'], [
            'icon' => 'fas fa-newspaper',
            'order' => 10,
            'is_active' => true,
        ]);

        $newsChildren = [
            ['name' => 'News Articles', 'route' => 'admin.news.index', 'permission' => 'news.view', 'order' => 1],
            ['name' => 'News Categories', 'route' => 'news-categories.index', 'permission' => 'news-categories.view', 'order' => 2],
            ['name' => 'News Tags', 'route' => 'news-tags.index', 'permission' => 'news-tags.view', 'order' => 3],
            ['name' => 'News Comments', 'route' => 'news-comments.index', 'permission' => 'news-comments.view', 'order' => 4],
        ];

        foreach ($newsChildren as $child) {
            $this->firstOrCreateForApp('news', ['parent_id' => $newsDropdown->id, 'name' => $child['name']], [
                'type' => 'item',
                'route' => $child['route'],
                'permission' => $child['permission'],
                'order' => $child['order'],
                'is_active' => true,
            ]);
        }

        // 6. LP Compro Workers App Menus (ADMIN_MENU_APP=lp-compro-workers)
        $oldLpComproDropdown = Menu::query()
            ->forApp('lp-compro-workers')
            ->where('name', 'Company Profile')
            ->where('type', 'dropdown')
            ->first();

        if ($oldLpComproDropdown) {
            Menu::query()->where('parent_id', $oldLpComproDropdown->id)->delete();
            $oldLpComproDropdown->delete();
        }

        $this->firstOrCreateForApp('lp-compro-workers', ['name' => 'Dashboard', 'type' => 'item'], [
            'icon' => 'fas fa-fire',
            'route' => 'dashboard',
            'order' => 1,
            'is_active' => true,
        ]);

        // Recruitment & Catalog
        $this->firstOrCreateForApp('lp-compro-workers', ['name' => 'Recruitment & Catalog', 'type' => 'header'], [
            'order' => 5,
            'is_active' => true,
        ]);

        $this->firstOrCreateForApp('lp-compro-workers', ['name' => 'Workers', 'type' => 'item'], [
            'icon' => 'fas fa-users',
            'route' => 'lp-workers.index',
            'permission' => 'lp-workers.view',
            'order' => 10,
            'is_active' => true,
        ]);

        $this->firstOrCreateForApp('lp-compro-workers', ['name' => 'Services', 'type' => 'item'], [
            'icon' => 'fas fa-briefcase',
            'route' => 'lp-services.index',
            'permission' => 'lp-services.view',
            'order' => 15,
            'is_active' => true,
        ]);

        $this->firstOrCreateForApp('lp-compro-workers', ['name' => 'Recruitment Steps', 'type' => 'item'], [
            'icon' => 'fas fa-stream',
            'route' => 'lp-recruitment-steps.index',
            'permission' => 'lp-recruitment-steps.view',
            'order' => 20,
            'is_active' => true,
        ]);

        // Company & Pages
        $this->firstOrCreateForApp('lp-compro-workers', ['name' => 'Company & Pages', 'type' => 'header'], [
            'order' => 25,
            'is_active' => true,
        ]);

        $aboutDropdown = $this->firstOrCreateForApp('lp-compro-workers', ['name' => 'About & Identity', 'type' => 'dropdown'], [
            'icon' => 'fas fa-building',
            'order' => 30,
            'is_active' => true,
        ]);

        foreach ([
            ['name' => 'Company Introduction', 'route' => 'lp-company-introduction.edit', 'permission' => 'lp-company-introduction.edit', 'order' => 1],
            ['name' => 'Vision & Mission', 'route' => 'lp-vision-mission.edit', 'permission' => 'lp-vision-mission.edit', 'order' => 2],
            ['name' => 'Company Values', 'route' => 'lp-company-values.index', 'permission' => 'lp-company-values.view', 'order' => 3],
            ['name' => 'Milestones', 'route' => 'lp-milestones.index', 'permission' => 'lp-milestones.view', 'order' => 4],
            ['name' => 'Company Stats', 'route' => 'lp-company-stats.index', 'permission' => 'lp-company-stats.view', 'order' => 5],
        ] as $child) {
            $this->firstOrCreateForApp('lp-compro-workers', ['parent_id' => $aboutDropdown->id, 'name' => $child['name']], [
                'type' => 'item',
                'route' => $child['route'],
                'permission' => $child['permission'],
                'order' => $child['order'],
                'is_active' => true,
            ]);
        }

        $trustDropdown = $this->firstOrCreateForApp('lp-compro-workers', ['name' => 'Trust & Media', 'type' => 'dropdown'], [
            'icon' => 'fas fa-images',
            'order' => 35,
            'is_active' => true,
        ]);

        foreach ([
            ['name' => 'Why Choose Us', 'route' => 'lp-why-choose-us.index', 'permission' => 'lp-why-choose-us.view', 'order' => 1],
            ['name' => 'Testimonials', 'route' => 'lp-testimonials.index', 'permission' => 'lp-testimonials.view', 'order' => 2],
            ['name' => 'Galleries', 'route' => 'lp-galleries.index', 'permission' => 'lp-galleries.view', 'order' => 3],
            ['name' => 'FAQs', 'route' => 'lp-faqs.index', 'permission' => 'lp-faqs.view', 'order' => 4],
        ] as $child) {
            $this->firstOrCreateForApp('lp-compro-workers', ['parent_id' => $trustDropdown->id, 'name' => $child['name']], [
                'type' => 'item',
                'route' => $child['route'],
                'permission' => $child['permission'],
                'order' => $child['order'],
                'is_active' => true,
            ]);
        }

        // Communication
        $this->firstOrCreateForApp('lp-compro-workers', ['name' => 'Communication', 'type' => 'header'], [
            'order' => 40,
            'is_active' => true,
        ]);

        $this->firstOrCreateForApp('lp-compro-workers', ['name' => 'Contact Inquiries', 'type' => 'item'], [
            'icon' => 'fas fa-inbox',
            'route' => 'lp-contact-inquiries.index',
            'permission' => 'lp-contact-inquiries.view',
            'order' => 42,
            'is_active' => true,
        ]);

        $this->firstOrCreateForApp('lp-compro-workers', ['name' => 'Contact & Company Info', 'type' => 'item'], [
            'icon' => 'fas fa-address-card',
            'route' => 'lp-company-profile.edit',
            'permission' => 'lp-company-profile.edit',
            'order' => 45,
            'is_active' => true,
        ]);
    }

    /**
     * Find or create a menu item scoped to a specific app identifier.
     *
     * @param  array<string, mixed>  $match
     * @param  array<string, mixed>  $attributes
     */
    private function firstOrCreateForApp(string $app, array $match, array $attributes): Menu
    {
        $query = Menu::query()->forApp($app);
        foreach ($match as $column => $value) {
            $query->where($column, $value);
        }

        $existing = $query->first();
        if ($existing) {
            if (isset($attributes['icon']) && $existing->icon !== $attributes['icon']) {
                $existing->update(['icon' => $attributes['icon']]);
            }

            return $existing;
        }

        return Menu::query()->create(array_merge($match, $attributes, [
            'app' => [$app],
        ]));
    }
}
