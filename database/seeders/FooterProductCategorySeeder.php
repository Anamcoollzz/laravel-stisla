<?php

namespace Database\Seeders;

use App\Models\FooterProductCategory;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class FooterProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $desiredSlugs = [
            'fashion' => null,
            'cosmetics' => null,
            'bags' => 'Bags & Purse',
            'shoes' => null,
            'hat' => 'Hat & Accessories',
            'makeup' => null,
        ];

        $order = 1;
        foreach ($desiredSlugs as $slug => $customName) {
            $category = ProductCategory::query()->where('slug', $slug)->first();
            if (! $category) {
                continue;
            }

            FooterProductCategory::updateOrCreate(
                ['product_category_id' => $category->id],
                [
                    'custom_name' => $customName,
                    'order' => $order++,
                    'is_active' => true,
                ]
            );
        }

        if (FooterProductCategory::count() === 0) {
            $fallbackCategories = ProductCategory::query()->where('is_active', true)->orderBy('order')->take(6)->get();
            foreach ($fallbackCategories as $idx => $cat) {
                FooterProductCategory::updateOrCreate(
                    ['product_category_id' => $cat->id],
                    [
                        'custom_name' => null,
                        'order' => $idx + 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
