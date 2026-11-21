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

        Menu::query()
            ->where('name', 'Ecommerce Dashboard')
            ->where('route', 'dashboard.ecommerce')
            ->delete();

        // 2. SPP Payment App Menus (ADMIN_MENU_APP=spp-payment)
        $oldModulesDropdown = Menu::query()
            ->forApp('spp-payment')
            ->where('name', 'Modules')
            ->where('type', 'dropdown')
            ->first();

        if ($oldModulesDropdown) {
            Menu::query()->where('parent_id', $oldModulesDropdown->id)->delete();
            $oldModulesDropdown->delete();
        }

        Menu::query()
            ->forApp('spp-payment')
            ->where('name', 'Modules')
            ->where('type', 'header')
            ->delete();

        // Academic Section
        $this->firstOrCreateForApp('spp-payment', ['name' => 'Academic', 'type' => 'header'], [
            'order' => 10,
            'is_active' => true,
        ]);

        $academicItems = [
            ['name' => 'Classes', 'icon' => 'fas fa-school', 'route' => 'classes.index', 'permission' => 'classes.view', 'order' => 12],
            ['name' => 'Teachers', 'icon' => 'fas fa-chalkboard-teacher', 'route' => 'teachers.index', 'permission' => 'teachers.view', 'order' => 14],
            ['name' => 'Students', 'icon' => 'fas fa-user-graduate', 'route' => 'students.index', 'permission' => 'students.view', 'order' => 16],
            ['name' => 'Subjects', 'icon' => 'fas fa-book', 'route' => 'subjects.index', 'permission' => 'subjects.view', 'order' => 18],
            ['name' => 'Fields', 'icon' => 'fas fa-layer-group', 'route' => 'fields.index', 'permission' => 'fields.view', 'order' => 20],
        ];

        foreach ($academicItems as $item) {
            Menu::updateOrCreate(
                ['name' => $item['name'], 'type' => 'item', 'route' => $item['route']],
                [
                    'app' => ['spp-payment'],
                    'parent_id' => null,
                    'icon' => $item['icon'],
                    'permission' => $item['permission'],
                    'order' => $item['order'],
                    'is_active' => true,
                ]
            );
        }

        // Finance & Communication Section
        $this->firstOrCreateForApp('spp-payment', ['name' => 'Finance & Communication', 'type' => 'header'], [
            'order' => 25,
            'is_active' => true,
        ]);

        $financeCommItems = [
            ['name' => 'SPP Payments', 'icon' => 'fas fa-money-bill-wave', 'route' => 'spp-payments.index', 'permission' => 'spp-payments.view', 'order' => 27],
            ['name' => 'Tickets', 'icon' => 'fas fa-ticket-alt', 'route' => 'tickets.index', 'permission' => 'tickets.view', 'order' => 29],
        ];

        foreach ($financeCommItems as $item) {
            Menu::updateOrCreate(
                ['name' => $item['name'], 'type' => 'item', 'route' => $item['route']],
                [
                    'app' => ['spp-payment'],
                    'parent_id' => null,
                    'icon' => $item['icon'],
                    'permission' => $item['permission'],
                    'order' => $item['order'],
                    'is_active' => true,
                ]
            );
        }

        // Clean up previous Shop / Products dropdown if any
        $oldShopDropdown = Menu::query()
            ->where('name', 'Shop / Products')
            ->where('type', 'dropdown')
            ->first();

        if ($oldShopDropdown) {
            Menu::query()->where('parent_id', $oldShopDropdown->id)->delete();
            $oldShopDropdown->delete();
        }

        // Catalog & Products Section
        Menu::updateOrCreate(
            ['name' => 'Catalog & Products', 'type' => 'header'],
            ['order' => 41, 'is_active' => true]
        );

        $catalogItems = [
            ['name' => 'Products', 'icon' => 'fas fa-box-open', 'route' => 'products.index', 'permission' => 'products.view', 'order' => 42],
            ['name' => 'Categories', 'icon' => 'fas fa-tags', 'route' => 'product-categories.index', 'permission' => 'product-categories.view', 'order' => 43],
            ['name' => 'Brands', 'icon' => 'fas fa-copyright', 'route' => 'product-brands.index', 'permission' => 'product-brands.view', 'order' => 44],
            ['name' => 'Vendors', 'icon' => 'fas fa-store', 'route' => 'product-vendors.index', 'permission' => 'product-vendors.view', 'order' => 45],
            ['name' => 'Sizes', 'icon' => 'fas fa-ruler', 'route' => 'product-sizes.index', 'permission' => 'product-sizes.view', 'order' => 46],
            ['name' => 'Colors', 'icon' => 'fas fa-palette', 'route' => 'product-colors.index', 'permission' => 'product-colors.view', 'order' => 47],
            ['name' => 'Footer Categories', 'icon' => 'fas fa-list-ul', 'route' => 'footer-product-categories.index', 'permission' => 'footer-product-categories.view', 'order' => 48],
        ];

        foreach ($catalogItems as $item) {
            Menu::updateOrCreate(
                ['name' => $item['name'], 'type' => 'item', 'route' => $item['route']],
                [
                    'parent_id' => null,
                    'icon' => $item['icon'],
                    'permission' => $item['permission'],
                    'order' => $item['order'],
                    'is_active' => true,
                ]
            );
        }

        // Orders & Payments Section
        Menu::updateOrCreate(
            ['name' => 'Orders & Payments', 'type' => 'header'],
            ['order' => 49, 'is_active' => true]
        );

        $orderItems = [
            ['name' => 'Orders', 'icon' => 'fas fa-shopping-cart', 'route' => 'orders.index', 'permission' => 'orders.view', 'order' => 50],
            ['name' => 'Shipping Methods', 'icon' => 'fas fa-truck', 'route' => 'shipping-methods.index', 'permission' => 'shipping-methods.view', 'order' => 51],
            ['name' => 'Payment Methods', 'icon' => 'fas fa-credit-card', 'route' => 'payment-methods.index', 'permission' => 'payment-methods.view', 'order' => 52],
            ['name' => 'Payment Banks', 'icon' => 'fas fa-university', 'route' => 'payment-banks.index', 'permission' => 'payment-banks.view', 'order' => 53],
        ];

        foreach ($orderItems as $item) {
            Menu::updateOrCreate(
                ['name' => $item['name'], 'type' => 'item', 'route' => $item['route']],
                [
                    'parent_id' => null,
                    'icon' => $item['icon'],
                    'permission' => $item['permission'],
                    'order' => $item['order'],
                    'is_active' => true,
                ]
            );
        }

        // Content & Engagement Section
        Menu::updateOrCreate(
            ['name' => 'Content & Engagement', 'type' => 'header'],
            ['order' => 54, 'is_active' => true]
        );

        $contentItems = [
            ['name' => 'Banners', 'icon' => 'fas fa-images', 'route' => 'product-banners.index', 'permission' => 'product-banners.view', 'order' => 55],
            ['name' => 'Services', 'icon' => 'fas fa-concierge-bell', 'route' => 'store-services.index', 'permission' => 'store-services.view', 'order' => 56],
            ['name' => 'Our Team', 'icon' => 'fas fa-users', 'route' => 'our-teams.index', 'permission' => 'our-teams.view', 'order' => 57],
            ['name' => 'Blogs', 'icon' => 'fas fa-blog', 'route' => 'blogs.index', 'permission' => 'blogs.view', 'order' => 58],
            ['name' => 'Blog Comments', 'icon' => 'fas fa-comments', 'route' => 'blog-comments.index', 'permission' => 'blog-comments.view', 'order' => 59],
            ['name' => 'Testimonials', 'icon' => 'fas fa-quote-right', 'route' => 'testimonials.index', 'permission' => 'testimonials.view', 'order' => 60],
            ['name' => 'Product Reviews', 'icon' => 'fas fa-star', 'route' => 'product-reviews.index', 'permission' => 'product-reviews.view', 'order' => 61],
            ['name' => 'FAQs', 'icon' => 'fas fa-question-circle', 'route' => 'ecommerce-faqs.index', 'permission' => 'ecommerce-faqs.view', 'order' => 62],
        ];

        foreach ($contentItems as $item) {
            Menu::updateOrCreate(
                ['name' => $item['name'], 'type' => 'item', 'route' => $item['route']],
                [
                    'parent_id' => null,
                    'icon' => $item['icon'],
                    'permission' => $item['permission'],
                    'order' => $item['order'],
                    'is_active' => true,
                ]
            );
        }

        $allApps = ['online-shop', 'news', 'lp-compro-workers', 'spp-payment'];

        // Dashboard for spp-payment app
        $this->firstOrCreateForApp('spp-payment', ['name' => 'Dashboard', 'type' => 'item'], [
            'icon' => 'fas fa-fire',
            'route' => 'dashboard',
            'order' => 1,
            'is_active' => true,
        ]);

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
            ['app' => $allApps, 'order' => 70, 'is_active' => true]
        );

        $userManagementItems = [
            ['name' => 'Users', 'icon' => 'fas fa-users', 'route' => 'users.index', 'permission' => 'users.view', 'order' => 71],
            ['name' => 'Roles', 'icon' => 'fas fa-user-tag', 'route' => 'roles.index', 'permission' => 'roles.view', 'order' => 72],
            ['name' => 'Permissions', 'icon' => 'fas fa-key', 'route' => 'permissions.index', 'permission' => 'permissions.view', 'order' => 73],
            ['name' => 'Menu Management', 'icon' => 'fas fa-bars', 'route' => 'menus.index', 'permission' => 'menus.view', 'order' => 74],
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
            ['app' => $allApps, 'order' => 75, 'is_active' => true]
        );

        $systemItems = [
            ['name' => 'Database Backup', 'icon' => 'fas fa-database', 'route' => 'backups.index', 'permission' => 'backup.view', 'order' => 76],
            ['name' => 'Database Tables', 'icon' => 'fas fa-table', 'route' => 'database-tables.index', 'permission' => 'database-tables.view', 'order' => 77],
            ['name' => 'Log Request', 'icon' => 'fas fa-history', 'route' => 'log-requests.index', 'permission' => 'log-requests.view', 'order' => 78],
            ['name' => 'Activity Log', 'icon' => 'fas fa-clipboard-list', 'route' => 'activity-logs.index', 'permission' => 'activity-logs.view', 'order' => 79],
            ['name' => 'Laravel Logs', 'icon' => 'fas fa-file-alt', 'route' => 'laravel-logs.index', 'permission' => 'laravel-logs.view', 'order' => 80],
            ['name' => 'Announcements', 'icon' => 'fas fa-bullhorn', 'route' => 'announcements.index', 'permission' => 'announcements.view', 'order' => 81],
            ['name' => 'Notifications', 'icon' => 'fas fa-bell', 'route' => 'notifications.index', 'permission' => 'notifications.view', 'order' => 82],
            ['name' => 'Chat Box', 'icon' => 'fas fa-comments', 'route' => 'chat.index', 'permission' => 'chat.view', 'order' => 83],
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
            ['app' => $allApps, 'order' => 85, 'is_active' => true]
        );

        Menu::updateOrCreate(
            ['name' => 'Settings', 'type' => 'item', 'route' => 'settings.index'],
            [
                'app' => $allApps,
                'icon' => 'fas fa-cog',
                'permission' => 'settings.view',
                'order' => 90,
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
