<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\ProductVendor;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryTshirts = ProductCategory::where('name', 'T-shirts')->first();
        $categoryShoes = ProductCategory::where('name', 'Shoes')->first();
        $categoryBags = ProductCategory::where('name', 'Bags')->first();
        $categoryCake = ProductCategory::where('name', 'Cake')->first();

        $brandNike = ProductBrand::where('name', 'Nike')->first();
        $brandZara = ProductBrand::where('name', 'Zara')->first();
        $brandUniqlo = ProductBrand::where('name', 'Uniqlo')->first();

        $vendorFashion = ProductVendor::where('name', 'PT Mitra Busana Nusantara')->first();
        $vendorFood = ProductVendor::where('name', 'UD Sumber Makmur Fresh')->first();

        $sizeS = ProductSize::where('name', 'S')->first();
        $sizeM = ProductSize::where('name', 'M')->first();
        $sizeL = ProductSize::where('name', 'L')->first();
        $sizeXL = ProductSize::where('name', 'XL')->first();
        $sizeAll = ProductSize::where('name', 'All Size')->first();

        $colorBlack = ProductColor::where('name', 'Black')->first();
        $colorNavy = ProductColor::where('name', 'Navy Blue')->first();
        $colorRed = ProductColor::where('name', 'Crimson Red')->first();
        $colorWhite = ProductColor::where('name', 'White')->first();

        $products = [
            [
                'title' => 'Classic Cotton Crewneck T-Shirt',
                'slug' => 'classic-cotton-crewneck-t-shirt',
                'sku' => 'TSH-2026-0001',
                'price' => 189000,
                'discount_percent' => 15,
                'stock' => 120,
                'category_id' => $categoryTshirts?->id,
                'brand_id' => $brandUniqlo?->id,
                'vendor_id' => $vendorFashion?->id,
                'size_id' => $sizeM?->id,
                'color_id' => $colorNavy?->id,
                'short_description' => '<p>Kaos polos berbahan <strong>100% combed cotton 30s</strong> yang sangat adem, nyaman dipakai seharian untuk gaya kasual.</p>',
                'description_detail' => '<p>Didesain dengan potongan reguler modern dan jahitan rantai ganda pada leher dan pundak untuk ketahanan maksimal. Warna tahan cuci dan tidak mudah pudar.</p><ul><li>Bahan: 100% Premium Cotton Combed</li><li>Gramasi: 160 gsm</li><li>Pencucian: Mesin cuci suhu normal</li></ul>',
                'spesifications' => [
                    ['key' => 'Material', 'value' => '100% Combed Cotton 30s'],
                    ['key' => 'Fit', 'value' => 'Regular Fit'],
                    ['key' => 'Origin', 'value' => 'Made in Indonesia'],
                    ['key' => 'Care', 'value' => 'Machine Wash Cold'],
                ],
                'tags' => ['t-shirt', 'casual', 'cotton', 'summer', 'basics'],
                'thumbnail' => 'mantu/assets/img/product/5.jpg',
                'is_active' => true,
                'is_trending' => true,
                'is_new' => true,
                'is_sale' => false,
            ],
            [
                'title' => 'Nike Air Zoom Velocity Running Shoes',
                'slug' => 'nike-air-zoom-velocity-running-shoes',
                'sku' => 'SHO-2026-0002',
                'price' => 1650000,
                'discount_percent' => 10,
                'stock' => 45,
                'category_id' => $categoryShoes?->id,
                'brand_id' => $brandNike?->id,
                'vendor_id' => $vendorFashion?->id,
                'size_id' => $sizeL?->id,
                'color_id' => $colorBlack?->id,
                'short_description' => '<p>Sepatu lari berperforma tinggi dengan bantalan <em>Zoom Air</em> untuk kenyamanan ekstra dan daya dorong superior.</p>',
                'description_detail' => '<p>Sepatu running revolusioner dengan breathable mesh upper dan rubber outsole tahan aus. Cocok untuk lari harian maupun marathon.</p>',
                'spesifications' => [
                    ['key' => 'Cushioning', 'value' => 'Zoom Air Unit'],
                    ['key' => 'Weight', 'value' => '245g (Size 42)'],
                    ['key' => 'Terrain', 'value' => 'Road / Asphalt'],
                ],
                'tags' => ['nike', 'shoes', 'running', 'sport', 'zoom-air'],
                'thumbnail' => 'mantu/assets/img/product/9.jpg',
                'is_active' => true,
                'is_trending' => false,
                'is_new' => true,
                'is_sale' => false,
            ],
            [
                'title' => 'Urban Leather Crossbody Sling Bag',
                'slug' => 'urban-leather-crossbody-sling-bag',
                'sku' => 'BAG-2026-0003',
                'price' => 450000,
                'discount_percent' => 20,
                'stock' => 60,
                'category_id' => $categoryBags?->id,
                'brand_id' => $brandZara?->id,
                'vendor_id' => $vendorFashion?->id,
                'size_id' => $sizeAll?->id,
                'color_id' => $colorBlack?->id,
                'short_description' => '<p>Tas selempang kulit sintetis premium dengan kompartemen luas dan resleting YKK berkualitas.</p>',
                'description_detail' => '<p>Tampil trendi dan praktis dengan tas selempang serbaguna. Tahan cipratan air (water-repellent) dan dilengkapi sabuk pengikat yang dapat diatur panjangnya.</p>',
                'spesifications' => [
                    ['key' => 'Material', 'value' => 'High Grade PU Leather'],
                    ['key' => 'Dimensions', 'value' => '28 x 18 x 8 cm'],
                    ['key' => 'Zipper', 'value' => 'YKK Metal Zipper'],
                ],
                'tags' => ['bag', 'slingbag', 'leather', 'accessories', 'urban'],
                'thumbnail' => 'mantu/assets/img/product/15.jpg',
                'is_active' => true,
                'is_trending' => false,
                'is_new' => true,
                'is_sale' => true,
            ],
            [
                'title' => 'Cotton Fabric Summer Top',
                'slug' => 'cotton-fabric-summer-top',
                'sku' => 'TOP-2026-0004',
                'price' => 120000,
                'discount_percent' => 8,
                'stock' => 80,
                'category_id' => $categoryTshirts?->id,
                'brand_id' => $brandZara?->id,
                'vendor_id' => $vendorFashion?->id,
                'size_id' => $sizeS?->id,
                'color_id' => $colorWhite?->id,
                'short_description' => '<p>Atasan katun kasual ringan dan stylish untuk aktivitas luar ruangan di musim panas.</p>',
                'description_detail' => '<p>Terbuat dari serat katun alami yang mudah menyerap keringat dan lembut di kulit.</p>',
                'spesifications' => [
                    ['key' => 'Material', 'value' => 'Pure Cotton'],
                    ['key' => 'Pattern', 'value' => 'Plain'],
                ],
                'tags' => ['top', 'summer', 'fashion', 'women'],
                'thumbnail' => 'mantu/assets/img/product/1.jpg',
                'is_active' => true,
                'is_trending' => false,
                'is_new' => true,
                'is_sale' => false,
            ],
            [
                'title' => 'Mantu Premium Smart Watch',
                'slug' => 'mantu-premium-smart-watch',
                'sku' => 'WAT-2026-0005',
                'price' => 999000,
                'discount_percent' => 5,
                'stock' => 30,
                'category_id' => null,
                'brand_id' => null,
                'vendor_id' => $vendorFashion?->id,
                'size_id' => $sizeAll?->id,
                'color_id' => $colorBlack?->id,
                'short_description' => '<p>Jam tangan pintar dengan layar AMOLED jernih, sensor detak jantung, dan water resistance 5ATM.</p>',
                'description_detail' => '<p>Baterai tahan hingga 14 hari dalam sekali pengisian. Kompatibel dengan iOS dan Android.</p>',
                'spesifications' => [
                    ['key' => 'Display', 'value' => '1.43" AMOLED'],
                    ['key' => 'Battery', 'value' => '420mAh'],
                ],
                'tags' => ['watch', 'smartwatch', 'gadget', 'wearable'],
                'thumbnail' => 'mantu/assets/img/product/11.jpg',
                'is_active' => true,
                'is_trending' => false,
                'is_new' => false,
                'is_sale' => true,
            ],
            [
                'title' => 'Genuine Reversible Leather Belt',
                'slug' => 'genuine-reversible-leather-belt',
                'sku' => 'BLT-2026-0006',
                'price' => 150000,
                'discount_percent' => 20,
                'stock' => 90,
                'category_id' => null,
                'brand_id' => $brandUniqlo?->id,
                'vendor_id' => $vendorFashion?->id,
                'size_id' => $sizeAll?->id,
                'color_id' => $colorBlack?->id,
                'short_description' => '<p>Ikat pinggang kulit sapi asli dua sisi dengan gesper logam matte anti karat.</p>',
                'description_detail' => '<p>Bisa dibalik untuk menampilkan warna hitam atau cokelat sesuai setelan formal dan kasual Anda.</p>',
                'spesifications' => [
                    ['key' => 'Material', 'value' => 'Genuine Cowhide Leather'],
                    ['key' => 'Width', 'value' => '3.5 cm'],
                ],
                'tags' => ['belt', 'leather', 'men', 'accessories'],
                'thumbnail' => 'mantu/assets/img/product/13.jpg',
                'is_active' => true,
                'is_trending' => true,
                'is_new' => false,
                'is_sale' => true,
            ],
            [
                'title' => 'Artisan Red Velvet Birthday Cake',
                'slug' => 'artisan-red-velvet-birthday-cake',
                'sku' => 'BAK-2026-0004',
                'price' => 275000,
                'discount_percent' => 0,
                'stock' => 25,
                'category_id' => $categoryCake?->id,
                'brand_id' => null,
                'vendor_id' => $vendorFood?->id,
                'size_id' => $sizeAll?->id,
                'color_id' => $colorRed?->id,
                'short_description' => '<p>Kue Red Velvet lembut dengan lapisan <strong>cream cheese frosting</strong> gurih manis yang meleleh di mulut.</p>',
                'description_detail' => '<p>Dibuat segar setiap hari dengan bahan-bahan butter impor berkualitas tinggi. Tanpa bahan pengawet buatan.</p>',
                'spesifications' => [
                    ['key' => 'Diameter', 'value' => '20 cm'],
                    ['key' => 'Portion', 'value' => '8-10 Slices'],
                    ['key' => 'Shelf Life', 'value' => '3 Days Refrigerated'],
                ],
                'tags' => ['cake', 'bakery', 'red-velvet', 'dessert', 'birthday'],
                'thumbnail' => 'mantu/assets/img/product/4.jpg',
                'is_active' => true,
                'is_trending' => true,
                'is_new' => true,
                'is_sale' => false,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['sku' => $product['sku']], $product);
        }
    }
}
