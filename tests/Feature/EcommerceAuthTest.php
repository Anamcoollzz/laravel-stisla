<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest can access ecommerce login page', function () {
    $response = $this->get(route('ecommerce.login'));

    $response->assertOk()
        ->assertViewIs('ecommerce.pages.login')
        ->assertSee('Login Page')
        ->assertSee('Email Address*');
});

test('user can authenticate via ecommerce login form', function () {
    $user = User::factory()->create([
        'email' => 'customer@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post(route('ecommerce.login.submit'), [
        'email' => 'customer@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('ecommerce.shop'));
    $this->assertAuthenticatedAs($user);
});

test('login fails with invalid credentials', function () {
    User::factory()->create([
        'email' => 'valid@example.com',
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->from(route('ecommerce.login'))->post(route('ecommerce.login.submit'), [
        'email' => 'valid@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect(route('ecommerce.login'));
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('authenticated user is redirected away from ecommerce login page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('ecommerce.login'));

    $response->assertRedirect(route('ecommerce.shop'));
});

test('authenticated user can logout from ecommerce', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('ecommerce.logout'));

    $response->assertRedirect(route('ecommerce.login'));
    $this->assertGuest();
});

test('guest accessing cart is redirected to login with single warning alert message', function () {
    $response = $this->get(route('ecommerce.cart'));

    $response->assertRedirect(route('ecommerce.login'));
    $response->assertSessionHas('warning', 'Silakan login terlebih dahulu untuk mengakses keranjang belanja!');

    $followUp = $this->followRedirects($response);
    $followUp->assertOk();
    $followUp->assertSee('Silakan login terlebih dahulu untuk mengakses keranjang belanja!');

    $content = $followUp->getContent();
    $count = substr_count($content, 'Silakan login terlebih dahulu untuk mengakses keranjang belanja!');
    expect($count)->toBe(1);
});

test('guest accessing wishlist is redirected to login with single warning alert message', function () {
    $response = $this->get(route('ecommerce.wishlist'));

    $response->assertRedirect(route('ecommerce.login'));
    $response->assertSessionHas('warning', 'Silakan login terlebih dahulu untuk mengakses wishlist!');

    $followUp = $this->followRedirects($response);
    $followUp->assertOk();
    $followUp->assertSee('Silakan login terlebih dahulu untuk mengakses wishlist!');

    $content = $followUp->getContent();
    $count = substr_count($content, 'Silakan login terlebih dahulu untuk mengakses wishlist!');
    expect($count)->toBe(1);
});
