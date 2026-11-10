<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can toggle product into wishlist via ajax', function () {
    $product = Product::create([
        'title' => 'Comfortable Denim Shirt',
        'slug' => 'comfortable-denim-shirt',
        'sku' => 'CDS-999',
        'price' => 250000,
        'stock' => 15,
        'thumbnail' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&q=80',
        'is_active' => true,
    ]);

    $response = $this->postJson(route('ecommerce.wishlist.toggle', $product->id));

    $response->assertOk()
        ->assertJson([
            'status' => 'success',
            'action' => 'added',
            'is_wishlisted' => true,
            'count' => 1,
            'product_id' => $product->id,
        ])
        ->assertJsonStructure(['html']);

    expect(session('ecommerce_wishlist'))->toContain($product->id);
    expect($response->json('html'))->toContain('Comfortable Denim Shirt');
});

test('toggling already wishlisted product removes it from wishlist', function () {
    $product = Product::create([
        'title' => 'Striped Cotton Polo',
        'slug' => 'striped-cotton-polo',
        'sku' => 'SCP-888',
        'price' => 180000,
        'stock' => 20,
        'is_active' => true,
    ]);

    session()->put('ecommerce_wishlist', [$product->id]);

    $response = $this->postJson(route('ecommerce.wishlist.toggle', $product->id));

    $response->assertOk()
        ->assertJson([
            'status' => 'success',
            'action' => 'removed',
            'is_wishlisted' => false,
            'count' => 0,
            'product_id' => $product->id,
        ]);

    expect(session('ecommerce_wishlist'))->not->toContain($product->id);
    expect($response->json('html'))->toContain('Your Wishlist is empty!');
});

test('can remove product from wishlist via remove endpoint', function () {
    $product = Product::create([
        'title' => 'Leather Bifold Wallet',
        'slug' => 'leather-bifold-wallet',
        'sku' => 'LBW-777',
        'price' => 120000,
        'stock' => 10,
        'is_active' => true,
    ]);

    session()->put('ecommerce_wishlist', [$product->id]);

    $response = $this->postJson(route('ecommerce.wishlist.remove', $product->id));

    $response->assertOk()
        ->assertJson([
            'status' => 'success',
            'action' => 'removed',
            'is_wishlisted' => false,
            'count' => 0,
            'product_id' => $product->id,
        ]);

    expect(session('ecommerce_wishlist'))->toBeEmpty();
});

test('wishlist page displays products in session', function () {
    $product = Product::create([
        'title' => 'Chunky Sole Platform Shoes',
        'slug' => 'chunky-sole-platform-shoes',
        'sku' => 'CSP-666',
        'price' => 450000,
        'stock' => 8,
        'is_active' => true,
    ]);

    session()->put('ecommerce_wishlist', [$product->id]);

    $response = $this->get(route('ecommerce.wishlist'));

    $response->assertOk()
        ->assertViewIs('ecommerce.pages.wishlist')
        ->assertSee('Chunky Sole Platform Shoes')
        ->assertSee('Rp 450.000');
});

test('shop page renders wishlisted product with active heart icon', function () {
    $product = Product::create([
        'title' => 'Vintage Oversized Sunglasses',
        'slug' => 'vintage-oversized-sunglasses',
        'sku' => 'VOS-555',
        'price' => 150000,
        'stock' => 25,
        'is_active' => true,
    ]);

    session()->put('ecommerce_wishlist', [$product->id]);

    $response = $this->get(route('ecommerce.products.index'));

    $response->assertOk()
        ->assertSee('ri-heart-fill')
        ->assertSee('data-product-id="'.$product->id.'"', false);
});
