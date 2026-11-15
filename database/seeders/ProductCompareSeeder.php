<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCompare;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductCompareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->first();

        if (! $user) {
            return;
        }

        $products = Product::query()->active()->latest('id')->take(4)->get();
        $locations = ['In Store, Online', 'Online', 'In Store', 'In Store, Online'];
        $quantities = [1, 2, 1, 2];

        foreach ($products as $index => $product) {
            ProductCompare::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                ],
                [
                    'session_id' => null,
                    'location' => $locations[$index] ?? 'In Store, Online',
                    'quantity' => $quantities[$index] ?? 1,
                ]
            );
        }
    }
}
