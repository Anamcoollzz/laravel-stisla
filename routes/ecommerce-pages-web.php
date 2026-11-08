<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/ecommerce', '/ecommerce-pages/index.html');
Route::redirect('/ecommerce-pages', '/ecommerce-pages/index.html');

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
