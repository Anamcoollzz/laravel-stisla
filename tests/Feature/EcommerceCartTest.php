<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can add product to cart via ajax', function () {
    $product = Product::create([
        'title' => 'Canvas Sneakers',
        'slug' => 'canvas-sneakers',
        'sku' => 'CSN-101',
        'price' => 300000,
        'stock' => 10,
        'is_active' => true,
    ]);

    $response = $this->postJson(route('ecommerce.cart.add', $product->id), [
        'quantity' => 2,
    ]);

    $response->assertOk()
        ->assertJson([
            'status' => 'success',
            'count' => 2,
            'subtotal' => 600000,
            'product_id' => $product->id,
        ])
        ->assertJsonStructure(['html', 'subtotal_formatted']);

    $cart = session('ecommerce_cart');
    expect($cart)->toHaveKey($product->id);
    expect($cart[$product->id]['quantity'])->toBe(2);
    expect($cart[$product->id]['price'])->toBe(300000.0);
    expect($response->json('html'))->toContain('Canvas Sneakers');
});

test('adding product again increments quantity in cart', function () {
    $product = Product::create([
        'title' => 'Slim Fit Chinos',
        'slug' => 'slim-fit-chinos',
        'sku' => 'SFC-202',
        'price' => 200000,
        'stock' => 10,
        'is_active' => true,
    ]);

    $this->postJson(route('ecommerce.cart.add', $product->id), ['quantity' => 1]);
    $response = $this->postJson(route('ecommerce.cart.add', $product->id), ['quantity' => 3]);

    $response->assertOk()
        ->assertJson([
            'status' => 'success',
            'count' => 4,
            'subtotal' => 800000,
        ]);

    $cart = session('ecommerce_cart');
    expect($cart[$product->id]['quantity'])->toBe(4);
});

test('user can update product quantity in cart', function () {
    $product = Product::create([
        'title' => 'Casual Leather Belt',
        'slug' => 'casual-leather-belt',
        'sku' => 'CLB-303',
        'price' => 150000,
        'stock' => 10,
        'is_active' => true,
    ]);

    $this->postJson(route('ecommerce.cart.add', $product->id), ['quantity' => 1]);

    $response = $this->postJson(route('ecommerce.cart.update', $product->id), [
        'quantity' => 5,
    ]);

    $response->assertOk()
        ->assertJson([
            'status' => 'success',
            'count' => 5,
            'subtotal' => 750000,
        ]);

    $cart = session('ecommerce_cart');
    expect($cart[$product->id]['quantity'])->toBe(5);

    // Updating to 0 removes the item
    $removeResponse = $this->postJson(route('ecommerce.cart.update', $product->id), [
        'quantity' => 0,
    ]);

    $removeResponse->assertOk()
        ->assertJson([
            'status' => 'success',
            'count' => 0,
            'subtotal' => 0,
        ]);

    expect(session('ecommerce_cart'))->not->toHaveKey($product->id);
});

test('user can remove product from cart', function () {
    $product = Product::create([
        'title' => 'Winter Knit Beanie',
        'slug' => 'winter-knit-beanie',
        'sku' => 'WKB-404',
        'price' => 90000,
        'stock' => 5,
        'is_active' => true,
    ]);

    $this->postJson(route('ecommerce.cart.add', $product->id), ['quantity' => 2]);

    $response = $this->postJson(route('ecommerce.cart.remove', $product->id));

    $response->assertOk()
        ->assertJson([
            'status' => 'success',
            'count' => 0,
            'subtotal' => 0,
        ]);

    expect(session('ecommerce_cart'))->toBeEmpty();
});

test('cart page renders products from cart session', function () {
    $product = Product::create([
        'title' => 'Oversized Graphic Hoodie',
        'slug' => 'oversized-graphic-hoodie',
        'sku' => 'OGH-505',
        'price' => 350000,
        'stock' => 10,
        'is_active' => true,
    ]);

    $this->postJson(route('ecommerce.cart.add', $product->id), ['quantity' => 1]);

    $response = $this->get(route('ecommerce.cart'));

    $response->assertOk()
        ->assertViewIs('ecommerce.pages.cart')
        ->assertSee('Oversized Graphic Hoodie')
        ->assertSee('350.000');
});

test('cart index returns json when requested via ajax', function () {
    $product = Product::create([
        'title' => 'Striped Cotton Socks',
        'slug' => 'striped-cotton-socks',
        'sku' => 'SCS-606',
        'price' => 50000,
        'stock' => 20,
        'is_active' => true,
    ]);

    $this->postJson(route('ecommerce.cart.add', $product->id), ['quantity' => 3]);

    $response = $this->getJson(route('ecommerce.cart'));

    $response->assertOk()
        ->assertJson([
            'status' => 'success',
            'count' => 3,
            'subtotal' => 150000,
        ])
        ->assertJsonStructure(['items', 'html', 'subtotal_formatted']);
});
