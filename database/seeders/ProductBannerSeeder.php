<?php

namespace Database\Seeders;

use App\Models\ProductBanner;
use Illuminate\Database\Seeder;

class ProductBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'title' => "Fashion sale <br>for women's",
                'subtitle' => 'Elevate your every day. Style that speaks volumes.',
                'badge_label' => '50% Off',
                'button_text' => 'Shop Now',
                'button_url' => '/ecommerce-pages/shop-left-sidebar.html',
                'image' => null,
                'position' => 'hero',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => "Fashion sale <br>for Men's",
                'subtitle' => 'Wear the change. Fashion that feels good.',
                'badge_label' => '35% Off',
                'button_text' => 'Shop Now',
                'button_url' => '/ecommerce-pages/shop-left-sidebar.html',
                'image' => null,
                'position' => 'hero',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => "Fashion sale <br>for Children's",
                'subtitle' => 'Wear the change. Fashion that feels good.',
                'badge_label' => '44% Off',
                'button_text' => 'Shop Now',
                'button_url' => '/ecommerce-pages/shop-left-sidebar.html',
                'image' => null,
                'position' => 'hero',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => "Cosmetics sale <br>for Women's",
                'subtitle' => 'Wear the change. Fashion that feels good.',
                'badge_label' => '22% Off',
                'button_text' => 'Shop Now',
                'button_url' => '/ecommerce-pages/shop-left-sidebar.html',
                'image' => null,
                'position' => 'hero',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $banner) {
            ProductBanner::updateOrCreate(
                ['title' => $banner['title'], 'position' => $banner['position']],
                $banner
            );
        }
    }
}
