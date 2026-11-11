<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductBanner;
use Illuminate\Database\Seeder;

class ProductBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $womenProduct = Product::query()->active()->where('slug', 'floral-bohemian-summer-maxi-dress')->first()
            ?? Product::query()->active()->first();
        $menProduct = Product::query()->active()->where('slug', 'classic-oxford-slim-long-sleeve-shirt')->first()
            ?? Product::query()->active()->skip(1)->first()
            ?? $womenProduct;
        $childProduct = Product::query()->active()->where('slug', 'french-terry-pullover-streetwear-hoodie')->first()
            ?? Product::query()->active()->skip(2)->first()
            ?? $womenProduct;
        $cosmeticProduct = Product::query()->active()->where('slug', 'velvet-matte-long-stay-liquid-lipstick')->first()
            ?? Product::query()->active()->skip(3)->first()
            ?? $womenProduct;

        $banners = [
            [
                'title' => "Fashion sale <br>for women's",
                'subtitle' => 'Elevate your every day. Style that speaks volumes.',
                'badge_label' => '50% Off',
                'button_text' => 'Shop Now',
                'product_id' => $womenProduct?->id,
                'button_url' => $womenProduct ? route('ecommerce.products.detail', $womenProduct->slug, false) : '/ecommerce/products',
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
                'product_id' => $menProduct?->id,
                'button_url' => $menProduct ? route('ecommerce.products.detail', $menProduct->slug, false) : '/ecommerce/products',
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
                'product_id' => $childProduct?->id,
                'button_url' => $childProduct ? route('ecommerce.products.detail', $childProduct->slug, false) : '/ecommerce/products',
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
                'product_id' => $cosmeticProduct?->id,
                'button_url' => $cosmeticProduct ? route('ecommerce.products.detail', $cosmeticProduct->slug, false) : '/ecommerce/products',
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
