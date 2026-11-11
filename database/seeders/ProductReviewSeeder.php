<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::query()->active()->take(10)->get();
        if ($products->isEmpty()) {
            return;
        }

        $users = User::query()->take(5)->get();

        $sampleReviews = [
            [
                'name' => 'Mariya Lykra',
                'email' => 'mariya.lykra@example.com',
                'rating' => 5,
                'comment' => 'Produk ini luar biasa! Bahan sangat nyaman dan jahitan rapi sekali. Pengiriman cepat dan packaging sangat aman.',
                'is_approved' => true,
            ],
            [
                'name' => 'Moris Willson',
                'email' => 'moris.w@example.com',
                'rating' => 4,
                'comment' => 'Kualitas sangat memuaskan sesuai dengan foto dan deskripsi. Ukurannya pas dan warnanya bagus.',
                'is_approved' => true,
            ],
            [
                'name' => 'Sarah Connor',
                'email' => 'sarah.c@example.com',
                'rating' => 5,
                'comment' => 'Sangat recommended! Sudah pembelian kedua dan pelayanannya tetap konsisten terbaik.',
                'is_approved' => true,
            ],
            [
                'name' => 'David Beckham',
                'email' => 'david.b@example.com',
                'rating' => 5,
                'comment' => 'Desain modern, sangat cocok untuk pemakaian sehari-hari maupun acara kasual. 10/10.',
                'is_approved' => true,
            ],
            [
                'name' => 'Jessica Alba',
                'email' => 'jessica.a@example.com',
                'rating' => 4,
                'comment' => 'Bagus banget, bahannya adem dan tidak mudah kusut. Pasti akan order lagi model lainnya.',
                'is_approved' => true,
            ],
        ];

        foreach ($products as $index => $product) {
            // Add 2 reviews per product
            $rev1 = $sampleReviews[$index % count($sampleReviews)];
            $rev2 = $sampleReviews[($index + 1) % count($sampleReviews)];

            ProductReview::updateOrCreate(
                ['product_id' => $product->id, 'email' => $rev1['email']],
                array_merge($rev1, [
                    'product_id' => $product->id,
                    'user_id' => $users->isNotEmpty() ? $users->first()->id : null,
                ])
            );

            ProductReview::updateOrCreate(
                ['product_id' => $product->id, 'email' => $rev2['email']],
                array_merge($rev2, [
                    'product_id' => $product->id,
                    'user_id' => $users->count() > 1 ? $users->get(1)->id : null,
                ])
            );
        }
    }
}

