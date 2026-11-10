<?php

use App\Http\Controllers\Ecommerce\ProductController as EcommerceProductController;
use Illuminate\Support\Facades\Route;

Route::redirect('/ecommerce', '/ecommerce-pages/index.html');
Route::redirect('/ecommerce-pages', '/ecommerce-pages/index.html');

Route::get('/ecommerce/products', [EcommerceProductController::class, 'index'])
    ->name('ecommerce.products.index');
Route::get('/ecommerce/shop', [EcommerceProductController::class, 'index'])
    ->name('ecommerce.shop');
Route::get('/ecommerce/category/{category:slug}', [EcommerceProductController::class, 'category'])
    ->name('ecommerce.category');

// Route::get('/ecommerce-pages/shop-right-sidebar.html', [EcommerceProductController::class, 'index'])
//     ->name('ecommerce.shop-right-sidebar.html');
// Route::get('/ecommerce-pages/shop-right-sidebar', [EcommerceProductController::class, 'index'])
//     ->name('ecommerce.shop-right-sidebar');

Route::get('/ecommerce/products/{slug}', [EcommerceProductController::class, 'show'])
    ->name('ecommerce.products.detail');

// Ecommerce Demo Pages (Mantu HTML Template)
$ecommercePages = [
    'about-us',
    'blog-detail-full-width',
    'blog-detail-right-sidebar',
    'blog-full-width',
    'blog-right-sidebar',
    'cart',
    'checkout',
    'compare',
    'contact-us',
    'faq',
    'index',
    'login',
    'product-accordion-full-width',
    'product-accordion',
    'product-detail',
    'product-full-width',
    'register',
    'shop-banner-full-width',
    'shop-banner-left-sidebar',
    'shop-banner-right-sidebar',
    'shop-full-width',
    'shop-left-sidebar',
    'shop-list-banner-full-width',
    'shop-list-banner-left-sidebar',
    'shop-list-banner-right-sidebar',
    'shop-list-full-width',
    'shop-list-left-sidebar',
    'shop-list-right-sidebar',
    'shop-right-sidebar',
    'terms',
    'track-order',
    'wishlist',
];

foreach ($ecommercePages as $page) {
    Route::view("/ecommerce-pages/{$page}.html", "ecommerce.pages.{$page}")
        ->name("ecommerce.{$page}.html");
    Route::view("/ecommerce-pages/{$page}", "ecommerce.pages.{$page}")
        ->name("ecommerce.{$page}");
}
