<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $structure = [
            [
                'name' => 'Fashion',
                'order' => 1,
                'children' => [
                    [
                        'name' => 'Clothes',
                        'icon' => 'mantu/assets/img/icons/clothes-2.svg',
                        'order' => 1,
                        'children' => [
                            ['name' => 'T-shirts', 'route' => 'ecommerce.products.index', 'order' => 1],
                            ['name' => 'Shirts', 'route' => 'ecommerce.products.index', 'order' => 2],
                            ['name' => 'gowns', 'route' => 'ecommerce.products.index', 'order' => 3],
                            ['name' => 'Dresses', 'route' => 'ecommerce.products.index', 'order' => 4],
                            ['name' => 'sharees', 'route' => 'ecommerce.products.index', 'order' => 5],
                            ['name' => 'jeans', 'route' => 'ecommerce.products.index', 'order' => 6],
                        ],
                    ],
                    [
                        'name' => 'Shoes',
                        'icon' => 'mantu/assets/img/icons/shoes.svg',
                        'route' => 'ecommerce.products.index',
                        'order' => 2,
                    ],
                    [
                        'name' => 'glasses',
                        'icon' => 'mantu/assets/img/icons/glasses.svg',
                        'route' => 'ecommerce.products.index',
                        'order' => 3,
                    ],
                    [
                        'name' => 'Bags',
                        'icon' => 'mantu/assets/img/icons/bag.svg',
                        'order' => 4,
                        'children' => [
                            ['name' => 'Purse', 'route' => 'ecommerce.products.index', 'order' => 1],
                            ['name' => 'Bags', 'route' => 'ecommerce.products.index', 'order' => 2],
                            ['name' => 'wallet', 'route' => 'ecommerce.products.index', 'order' => 3],
                        ],
                    ],
                    [
                        'name' => 'Hat',
                        'icon' => 'mantu/assets/img/icons/hat.svg',
                        'route' => 'ecommerce.products.index',
                        'order' => 5,
                    ],
                    [
                        'name' => 'Makeup',
                        'icon' => 'mantu/assets/img/icons/makeup.svg',
                        'order' => 6,
                        'children' => [
                            ['name' => 'Lipstick', 'route' => 'ecommerce.products.index', 'order' => 1],
                            ['name' => 'eye liner', 'route' => 'ecommerce.products.index', 'order' => 2],
                            ['name' => 'nail paint', 'route' => 'ecommerce.products.index', 'order' => 3],
                            ['name' => 'Makeup kit', 'route' => 'ecommerce.products.index', 'order' => 4],
                        ],
                    ],
                    [
                        'name' => 'Cosmetics',
                        'icon' => 'mantu/assets/img/icons/cosmetics.svg',
                        'order' => 7,
                        'children' => [
                            ['name' => 'Shampoo', 'route' => 'ecommerce.products.index', 'order' => 1],
                            ['name' => 'face wash', 'route' => 'ecommerce.products.index', 'order' => 2],
                            ['name' => 'body wash', 'route' => 'ecommerce.products.index', 'order' => 3],
                            ['name' => 'sunscreen', 'route' => 'ecommerce.products.index', 'order' => 4],
                            ['name' => 'serum', 'route' => 'ecommerce.products.index', 'order' => 5],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Bakery',
                'order' => 2,
                'children' => [
                    [
                        'name' => 'Cake',
                        'icon' => 'mantu/assets/img/icons/cake.svg',
                        'order' => 1,
                        'children' => [
                            ['name' => 'cup cake', 'route' => 'ecommerce.products.index', 'order' => 1],
                            ['name' => 'pastry', 'route' => 'ecommerce.products.index', 'order' => 2],
                            ['name' => 'Cake', 'route' => 'ecommerce.products.index', 'order' => 3],
                        ],
                    ],
                    [
                        'name' => 'Bread',
                        'icon' => 'mantu/assets/img/icons/bread.svg',
                        'route' => 'ecommerce.products.index',
                        'order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Vegetables',
                'order' => 3,
                'children' => [
                    [
                        'name' => 'tuber root',
                        'icon' => 'mantu/assets/img/icons/tuber.svg',
                        'order' => 1,
                        'children' => [
                            ['name' => 'Sweet Potato', 'route' => 'ecommerce.products.index', 'order' => 1],
                            ['name' => 'Ginger', 'route' => 'ecommerce.products.index', 'order' => 2],
                            ['name' => 'cassava', 'route' => 'ecommerce.products.index', 'order' => 3],
                        ],
                    ],
                    [
                        'name' => 'Tomato',
                        'icon' => 'mantu/assets/img/icons/tomato.svg',
                        'route' => 'ecommerce.products.index',
                        'order' => 2,
                    ],
                    [
                        'name' => 'Lemon',
                        'icon' => 'mantu/assets/img/icons/lemon.svg',
                        'route' => 'ecommerce.products.index',
                        'order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Fruits',
                'order' => 4,
                'children' => [
                    [
                        'name' => 'avocado',
                        'icon' => 'mantu/assets/img/icons/avocado.svg',
                        'route' => 'ecommerce.products.index',
                        'order' => 1,
                    ],
                    [
                        'name' => 'strawberry',
                        'icon' => 'mantu/assets/img/icons/strawberry.svg',
                        'route' => 'ecommerce.products.index',
                        'order' => 2,
                    ],
                    [
                        'name' => 'cherry',
                        'icon' => 'mantu/assets/img/icons/cherry.svg',
                        'route' => 'ecommerce.products.index',
                        'order' => 3,
                    ],
                    [
                        'name' => 'Lychee',
                        'icon' => 'mantu/assets/img/icons/lychee.svg',
                        'route' => 'ecommerce.products.index',
                        'order' => 4,
                    ],
                ],
            ],
        ];

        foreach ($structure as $level1) {
            $l1 = ProductCategory::firstOrCreate(
                ['name' => $level1['name'], 'parent_id' => null],
                [
                    'slug' => ProductCategory::generateUniqueSlug($level1['name']),
                    'order' => $level1['order'],
                    'is_active' => true,
                ]
            );

            foreach ($level1['children'] ?? [] as $level2) {
                $l2 = ProductCategory::firstOrCreate(
                    ['name' => $level2['name'], 'parent_id' => $l1->id],
                    [
                        'slug' => ProductCategory::generateUniqueSlug($level2['name']),
                        'icon' => $level2['icon'] ?? null,
                        'route' => $level2['route'] ?? null,
                        'order' => $level2['order'],
                        'is_active' => true,
                    ]
                );

                foreach ($level2['children'] ?? [] as $level3) {
                    ProductCategory::firstOrCreate(
                        ['name' => $level3['name'], 'parent_id' => $l2->id],
                        [
                            'slug' => ProductCategory::generateUniqueSlug($level3['name']),
                            'route' => $level3['route'] ?? null,
                            'order' => $level3['order'],
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
