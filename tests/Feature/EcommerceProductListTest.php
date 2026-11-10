<?php

use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest can access ecommerce product list page', function () {
    $category = ProductCategory::create([
        'name' => 'Fashion',
        'slug' => 'fashion',
        'is_active' => true,
    ]);

    $product = Product::create([
        'category_id' => $category->id,
        'title' => 'Vintage Denim Jacket',
        'slug' => 'vintage-denim-jacket',
        'sku' => 'VDJ-001',
        'price' => 150000,
        'stock' => 10,
        'is_active' => true,
    ]);

    $this->get(route('ecommerce.products.index'))
        ->assertOk()
        ->assertViewIs('ecommerce.pages.shop')
        ->assertSee('Vintage Denim Jacket')
        ->assertSee($category->name);
});

test('products can be filtered by category slug', function () {
    $categoryClothes = ProductCategory::create([
        'name' => 'Clothes',
        'slug' => 'clothes',
        'is_active' => true,
    ]);

    $categoryShoes = ProductCategory::create([
        'name' => 'Shoes',
        'slug' => 'shoes',
        'is_active' => true,
    ]);

    $productClothes = Product::create([
        'category_id' => $categoryClothes->id,
        'title' => 'Silk Summer Shirt',
        'slug' => 'silk-summer-shirt',
        'sku' => 'SSS-001',
        'price' => 200000,
        'stock' => 5,
        'is_active' => true,
    ]);

    $productShoes = Product::create([
        'category_id' => $categoryShoes->id,
        'title' => 'Running Sneakers Pro',
        'slug' => 'running-sneakers-pro',
        'sku' => 'RSP-002',
        'price' => 450000,
        'stock' => 8,
        'is_active' => true,
    ]);

    $response = $this->get(route('ecommerce.products.index', ['category' => 'clothes']));

    $response->assertOk()
        ->assertSee('Silk Summer Shirt')
        ->assertDontSee('Running Sneakers Pro')
        ->assertSee('Category: Clothes');
});

test('category route alias resolves and filters products properly', function () {
    $category = ProductCategory::create([
        'name' => 'Bakery',
        'slug' => 'bakery',
        'is_active' => true,
    ]);

    $product = Product::create([
        'category_id' => $category->id,
        'title' => 'Chocolate Cupcake',
        'slug' => 'chocolate-cupcake',
        'sku' => 'CC-001',
        'price' => 25000,
        'stock' => 20,
        'is_active' => true,
    ]);

    $this->get(route('ecommerce.category', $category->slug))
        ->assertOk()
        ->assertSee('Chocolate Cupcake');
});

test('products can be filtered by brand', function () {
    $brandA = ProductBrand::create(['name' => 'Brand Alpha', 'slug' => 'brand-alpha']);
    $brandB = ProductBrand::create(['name' => 'Brand Beta', 'slug' => 'brand-beta']);

    Product::create([
        'brand_id' => $brandA->id,
        'title' => 'Alpha Jacket',
        'slug' => 'alpha-jacket',
        'sku' => 'AJ-001',
        'price' => 300000,
        'stock' => 10,
        'is_active' => true,
    ]);

    Product::create([
        'brand_id' => $brandB->id,
        'title' => 'Beta Shoes',
        'slug' => 'beta-shoes',
        'sku' => 'BS-002',
        'price' => 350000,
        'stock' => 10,
        'is_active' => true,
    ]);

    $this->get(route('ecommerce.products.index', ['brand' => 'brand-alpha']))
        ->assertOk()
        ->assertSee('Alpha Jacket')
        ->assertDontSee('Beta Shoes');

    // Test with category + string brand slug (reproducing Oracle ORA-01722 scenario)
    $category = ProductCategory::create(['name' => 'Sportswear', 'slug' => 'sportswear', 'is_active' => true]);
    $productWithCat = Product::create([
        'category_id' => $category->id,
        'brand_id' => $brandA->id,
        'title' => 'Alpha Sport Hooded Top',
        'slug' => 'alpha-sport-hooded-top',
        'sku' => 'ASHT-003',
        'price' => 250000,
        'stock' => 5,
        'is_active' => true,
    ]);

    $this->get(route('ecommerce.products.index', ['category' => 'sportswear', 'brand' => 'brand-alpha']))
        ->assertOk()
        ->assertSee('Alpha Sport Hooded Top')
        ->assertDontSee('Beta Shoes');
});

test('app ecommerce layout categories link to product list with category', function () {
    $category = ProductCategory::create([
        'name' => 'Gadgets',
        'slug' => 'gadgets',
        'is_active' => true,
    ]);

    $this->get(route('ecommerce.index.html'))
        ->assertOk()
        ->assertSee(route('ecommerce.products.index', ['category' => 'gadgets']), false);
});

test('products can be sorted by price and name', function () {
    Product::create([
        'title' => 'Expensive Watch',
        'slug' => 'expensive-watch',
        'sku' => 'EW-001',
        'price' => 500000,
        'stock' => 5,
        'is_active' => true,
    ]);

    Product::create([
        'title' => 'Affordable Pen',
        'slug' => 'affordable-pen',
        'sku' => 'AP-002',
        'price' => 20000,
        'stock' => 50,
        'is_active' => true,
    ]);

    $responseLow = $this->get(route('ecommerce.products.index', ['sort' => 'price_low']));
    $responseLow->assertOk();
    $contentLow = $responseLow->getContent();
    expect(strpos($contentLow, 'Affordable Pen'))->toBeLessThan(strpos($contentLow, 'Expensive Watch'));

    $responseHigh = $this->get(route('ecommerce.products.index', ['sort' => 'price_high']));
    $responseHigh->assertOk();
    $contentHigh = $responseHigh->getContent();
    expect(strpos($contentHigh, 'Expensive Watch'))->toBeLessThan(strpos($contentHigh, 'Affordable Pen'));
});
