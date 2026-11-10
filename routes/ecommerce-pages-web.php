<?php

use App\Http\Controllers\Ecommerce\AuthController as EcommerceAuthController;
use App\Http\Controllers\Ecommerce\CartController as EcommerceCartController;
use App\Http\Controllers\Ecommerce\ProductController as EcommerceProductController;
use App\Http\Controllers\Ecommerce\WishlistController as EcommerceWishlistController;
use App\Models\OurTeam;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductVendor;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;

Route::redirect('/ecommerce', '/ecommerce-pages/index.html');
Route::redirect('/ecommerce-pages', '/ecommerce-pages/index.html');

Route::get('/ecommerce/products', [EcommerceProductController::class, 'index'])
    ->name('ecommerce.products.index');
Route::delete('/ecommerce/recent-searches', [EcommerceProductController::class, 'clearRecentSearches'])
    ->name('ecommerce.recent-searches.clear');
Route::post('/ecommerce/recent-searches/clear', [EcommerceProductController::class, 'clearRecentSearches'])
    ->name('ecommerce.recent-searches.clear.post');
Route::get('/ecommerce/shop', [EcommerceProductController::class, 'index'])
    ->name('ecommerce.shop');
Route::get('/ecommerce/category/{category:slug}', [EcommerceProductController::class, 'category'])
    ->name('ecommerce.category');

Route::get('/ecommerce/wishlist', [EcommerceWishlistController::class, 'index'])
    ->name('ecommerce.wishlist');
Route::get('/ecommerce-pages/wishlist.html', [EcommerceWishlistController::class, 'index'])
    ->name('ecommerce.wishlist.html');
Route::get('/ecommerce-pages/wishlist', [EcommerceWishlistController::class, 'index']);
Route::post('/ecommerce/wishlist/toggle/{product}', [EcommerceWishlistController::class, 'toggle'])
    ->name('ecommerce.wishlist.toggle');
Route::post('/ecommerce/wishlist/remove/{product}', [EcommerceWishlistController::class, 'remove'])
    ->name('ecommerce.wishlist.remove');
Route::delete('/ecommerce/wishlist/{product}', [EcommerceWishlistController::class, 'remove'])
    ->name('ecommerce.wishlist.destroy');

Route::get('/ecommerce/cart', [EcommerceCartController::class, 'index'])
    ->name('ecommerce.cart');
Route::get('/ecommerce-pages/cart.html', [EcommerceCartController::class, 'index'])
    ->name('ecommerce.cart.html');
Route::get('/ecommerce-pages/cart', [EcommerceCartController::class, 'index']);
Route::post('/ecommerce/cart/add/{product}', [EcommerceCartController::class, 'add'])
    ->name('ecommerce.cart.add');
Route::post('/ecommerce/cart/update/{product}', [EcommerceCartController::class, 'update'])
    ->name('ecommerce.cart.update');
Route::post('/ecommerce/cart/remove/{product}', [EcommerceCartController::class, 'remove'])
    ->name('ecommerce.cart.remove');
Route::delete('/ecommerce/cart/{product}', [EcommerceCartController::class, 'remove'])
    ->name('ecommerce.cart.destroy');

Route::get('/ecommerce/login', [EcommerceAuthController::class, 'showLoginForm'])
    ->name('ecommerce.login');
Route::post('/ecommerce/login', [EcommerceAuthController::class, 'login'])
    ->name('ecommerce.login.submit');
Route::post('/ecommerce/logout', [EcommerceAuthController::class, 'logout'])
    ->name('ecommerce.logout');
Route::get('/ecommerce-pages/login.html', [EcommerceAuthController::class, 'showLoginForm'])
    ->name('ecommerce.login.html');
Route::get('/ecommerce-pages/login', [EcommerceAuthController::class, 'showLoginForm']);

Route::get('/ecommerce/register', [EcommerceAuthController::class, 'showRegistrationForm'])
    ->name('ecommerce.register');
Route::post('/ecommerce/register', [EcommerceAuthController::class, 'register'])
    ->name('ecommerce.register.submit');
Route::get('/ecommerce-pages/register.html', [EcommerceAuthController::class, 'showRegistrationForm'])
    ->name('ecommerce.register.html');
Route::get('/ecommerce-pages/register', [EcommerceAuthController::class, 'showRegistrationForm']);

// Route::get('/ecommerce-pages/shop-right-sidebar.html', [EcommerceProductController::class, 'index'])
//     ->name('ecommerce.shop-right-sidebar.html');
// Route::get('/ecommerce-pages/shop-right-sidebar', [EcommerceProductController::class, 'index'])
//     ->name('ecommerce.shop-right-sidebar');

Route::get('/ecommerce/products/{slug}', [EcommerceProductController::class, 'show'])
    ->name('ecommerce.products.detail');

$aboutUsHandler = function () {
    $ourTeams = OurTeam::query()->active()->orderBy('order')->latest('id')->get();
    $about = Setting::getAboutData();
    $facts = [
        'vendors' => ProductVendor::count(),
        'brands' => ProductBrand::count(),
        'stock' => (int) Product::where('is_active', true)->sum('stock'),
        'products' => Product::where('is_active', true)->count(),
    ];

    return view('ecommerce.pages.about-us', compact('ourTeams', 'about', 'facts'));
};

Route::get('/ecommerce/about-us', $aboutUsHandler)->name('ecommerce.about-us');
Route::get('/ecommerce-pages/about-us.html', $aboutUsHandler)->name('ecommerce.about-us.html');
Route::get('/ecommerce-pages/about-us', $aboutUsHandler);

// Ecommerce Demo Pages (Mantu HTML Template)
$ecommercePages = [
    'blog-detail-full-width',
    'blog-detail-right-sidebar',
    'blog-full-width',
    'blog-right-sidebar',
    'checkout',
    'compare',
    'contact-us',
    'faq',
    'index',
    'product-accordion-full-width',
    'product-accordion',
    'product-detail',
    'product-full-width',
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
];

foreach ($ecommercePages as $page) {
    Route::view("/ecommerce-pages/{$page}.html", "ecommerce.pages.{$page}")
        ->name("ecommerce.{$page}.html");
    Route::view("/ecommerce-pages/{$page}", "ecommerce.pages.{$page}")
        ->name("ecommerce.{$page}");
}
