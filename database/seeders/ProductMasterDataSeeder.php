<?php

namespace Database\Seeders;

use App\Models\ProductBrand;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductVendor;
use Illuminate\Database\Seeder;

class ProductMasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Sizes
        $sizes = [
            ['name' => 'XS', 'code' => 'XS'],
            ['name' => 'S', 'code' => 'S'],
            ['name' => 'M', 'code' => 'M'],
            ['name' => 'L', 'code' => 'L'],
            ['name' => 'XL', 'code' => 'XL'],
            ['name' => 'XXL', 'code' => 'XXL'],
            ['name' => 'All Size', 'code' => 'ALL'],
        ];

        foreach ($sizes as $size) {
            ProductSize::firstOrCreate(['name' => $size['name']], $size);
        }

        // 2. Colors
        $colors = [
            ['name' => 'Black', 'color_code' => '#000000'],
            ['name' => 'White', 'color_code' => '#FFFFFF'],
            ['name' => 'Navy Blue', 'color_code' => '#001F3F'],
            ['name' => 'Crimson Red', 'color_code' => '#DC143C'],
            ['name' => 'Emerald Green', 'color_code' => '#50C878'],
            ['name' => 'Charcoal Gray', 'color_code' => '#36454F'],
            ['name' => 'Pastel Pink', 'color_code' => '#FFD1DC'],
            ['name' => 'Mustard Yellow', 'color_code' => '#FFDB58'],
        ];

        foreach ($colors as $color) {
            ProductColor::firstOrCreate(['name' => $color['name']], $color);
        }

        // 3. Brands
        $brands = [
            ['name' => 'Nike', 'slug' => 'nike'],
            ['name' => 'Adidas', 'slug' => 'adidas'],
            ['name' => 'Zara', 'slug' => 'zara'],
            ['name' => 'H&M', 'slug' => 'h-and-m'],
            ['name' => 'Uniqlo', 'slug' => 'uniqlo'],
            ['name' => 'Apple', 'slug' => 'apple'],
            ['name' => 'Samsung', 'slug' => 'samsung'],
            ['name' => 'Sony', 'slug' => 'sony'],
        ];

        foreach ($brands as $brand) {
            ProductBrand::firstOrCreate(['name' => $brand['name']], $brand);
        }

        // 4. Vendors
        $vendors = [
            [
                'name' => 'PT Mitra Busana Nusantara',
                'phone' => '+62 812-3456-7890',
                'email' => 'sales@mitrabusana.co.id',
                'address' => 'Jl. Jend. Sudirman Kav. 45, Jakarta Selatan',
                'description' => 'Supplier resmi fashion garment, tekstil premium, dan aksesoris fashion.',
            ],
            [
                'name' => 'CV Sentosa Jaya Abadi',
                'phone' => '+62 823-4567-8901',
                'email' => 'contact@sentosajaya.com',
                'address' => 'Jl. Riau No. 120, Bandung, Jawa Barat',
                'description' => 'Distributor terpercaya barang elektronik, gadget, dan perlengkapan harian.',
            ],
            [
                'name' => 'UD Sumber Makmur Fresh',
                'phone' => '+62 834-5678-9012',
                'email' => 'info@sumbermakmur.id',
                'address' => 'Jl. Kaliurang Km 9, Yogyakarta',
                'description' => 'Penyedia bahan kue, bakery, sayuran segar, dan buah-buahan lokal pilihan.',
            ],
        ];

        foreach ($vendors as $vendor) {
            ProductVendor::firstOrCreate(['name' => $vendor['name']], $vendor);
        }
    }
}
