<?php

use App\Http\Controllers\Ecommerce\AddressController as EcommerceAddressController;
use App\Http\Controllers\Ecommerce\AuthController as EcommerceAuthController;
use App\Http\Controllers\Ecommerce\BlogController as EcommerceBlogController;
use App\Http\Controllers\Ecommerce\CartController as EcommerceCartController;
use App\Http\Controllers\Ecommerce\CheckoutController as EcommerceCheckoutController;
use App\Http\Controllers\Ecommerce\CompareController as EcommerceCompareController;
use App\Http\Controllers\Ecommerce\OrderController as EcommerceOrderController;
use App\Http\Controllers\Ecommerce\ProductController as EcommerceProductController;
use App\Http\Controllers\Ecommerce\TicketController as EcommerceTicketController;
use App\Http\Controllers\Ecommerce\WishlistController as EcommerceWishlistController;
use App\Models\Blog;
use App\Models\EcommerceFaq;
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

Route::get('/ecommerce/checkout', [EcommerceCheckoutController::class, 'index'])
    ->name('ecommerce.checkout');
Route::post('/ecommerce/checkout', [EcommerceCheckoutController::class, 'store'])
    ->name('ecommerce.checkout.store');
Route::get('/ecommerce/checkout/detail/{orderNumber}', [EcommerceCheckoutController::class, 'show'])
    ->name('ecommerce.checkout.detail');
Route::post('/ecommerce/checkout/detail/{orderNumber}/payment-proof', [EcommerceOrderController::class, 'uploadPaymentProof'])
    ->name('ecommerce.checkout.payment-proof');
Route::get('/ecommerce/checkout/success/{orderNumber}', [EcommerceCheckoutController::class, 'show'])
    ->name('ecommerce.checkout.success');
Route::get('/ecommerce/order-list', [EcommerceOrderController::class, 'index'])
    ->name('ecommerce.order-list');
Route::get('/ecommerce-pages/checkout.html', [EcommerceCheckoutController::class, 'index'])
    ->name('ecommerce.checkout.html');
Route::get('/ecommerce-pages/checkout', [EcommerceCheckoutController::class, 'index']);

Route::get('/ecommerce/address-list', [EcommerceAddressController::class, 'index'])
    ->name('ecommerce.address-list');
Route::post('/ecommerce/address-list', [EcommerceAddressController::class, 'store'])
    ->name('ecommerce.address-list.store');
Route::put('/ecommerce/address-list/{address}', [EcommerceAddressController::class, 'update'])
    ->name('ecommerce.address-list.update');
Route::delete('/ecommerce/address-list/{address}', [EcommerceAddressController::class, 'destroy'])
    ->name('ecommerce.address-list.destroy');
Route::post('/ecommerce/address-list/{address}/default', [EcommerceAddressController::class, 'toggleDefault'])
    ->name('ecommerce.address-list.default');

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

Route::post('/ecommerce/products/{slug}/review', [EcommerceProductController::class, 'storeReview'])
    ->name('ecommerce.products.review.store');

Route::get('/ecommerce/blogs', [EcommerceBlogController::class, 'index'])
    ->name('ecommerce.blogs.index');

Route::get('/ecommerce/blog/{slug}', [EcommerceBlogController::class, 'show'])
    ->name('ecommerce.blogs.detail');

Route::post('/ecommerce/blog/{slug}/comment', [EcommerceBlogController::class, 'storeComment'])
    ->name('ecommerce.blogs.comment.store');

Route::get('/ecommerce-pages/blog-full-width.html', [EcommerceBlogController::class, 'index'])
    ->name('ecommerce.blog-full-width.html');

Route::get('/ecommerce-pages/blog-full-width', [EcommerceBlogController::class, 'index'])
    ->name('ecommerce.blog-full-width');

Route::get('/ecommerce-pages/blog-detail-right-sidebar.html', function () {
    $firstBlog = Blog::query()->active()->orderBy('order')->first();
    if ($firstBlog) {
        return redirect()->route('ecommerce.blogs.detail', $firstBlog->slug);
    }

    return view('ecommerce.pages.blog-detail');
})->name('ecommerce.blog-detail-right-sidebar.html');

Route::get('/ecommerce-pages/blog-detail-right-sidebar', function () {
    $firstBlog = Blog::query()->active()->orderBy('order')->first();
    if ($firstBlog) {
        return redirect()->route('ecommerce.blogs.detail', $firstBlog->slug);
    }

    return view('ecommerce.pages.blog-detail');
});

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

$faqHandler = function () {
    $faqs = EcommerceFaq::query()->active()->orderBy('order')->latest('id')->get();
    $leftFaqs = $faqs->where('column', 'left')->values();
    $rightFaqs = $faqs->where('column', 'right')->values();

    return view('ecommerce.pages.faq', compact('faqs', 'leftFaqs', 'rightFaqs'));
};

Route::get('/ecommerce/faq', $faqHandler)->name('ecommerce.faq');
Route::get('/ecommerce-pages/faq.html', $faqHandler)->name('ecommerce.faq.html');
Route::get('/ecommerce-pages/faq', $faqHandler);

Route::get('/ecommerce/compare', [EcommerceCompareController::class, 'index'])
    ->name('ecommerce.compare');
Route::get('/ecommerce-pages/compare.html', [EcommerceCompareController::class, 'index'])
    ->name('ecommerce.compare.html');
Route::get('/ecommerce-pages/compare', [EcommerceCompareController::class, 'index']);
Route::post('/ecommerce/compare/toggle/{product}', [EcommerceCompareController::class, 'toggle'])
    ->name('ecommerce.compare.toggle');
Route::post('/ecommerce/compare/remove/{product}', [EcommerceCompareController::class, 'remove'])
    ->name('ecommerce.compare.remove');
Route::delete('/ecommerce/compare/{product}', [EcommerceCompareController::class, 'remove'])
    ->name('ecommerce.compare.destroy');

$contactUsHandler = function () {
    $siteContact = Setting::getContactData();

    return view('ecommerce.pages.contact-us', compact('siteContact'));
};

Route::get('/ecommerce/contact-us', $contactUsHandler)->name('ecommerce.contact-us');
Route::post('/ecommerce/contact-us', [EcommerceTicketController::class, 'storeFromContact'])
    ->name('ecommerce.contact-us.submit');
Route::get('/ecommerce/ticket-list', [EcommerceTicketController::class, 'index'])
    ->name('ecommerce.ticket-list');
Route::post('/ecommerce/ticket-list/{ticket}/reply', [EcommerceTicketController::class, 'reply'])
    ->name('ecommerce.ticket-list.reply');
Route::get('/ecommerce-pages/contact-us.html', $contactUsHandler)->name('ecommerce.contact-us.html');
Route::get('/ecommerce-pages/contact-us', $contactUsHandler);

// Ecommerce Demo Pages (Mantu HTML Template)
$ecommercePages = [
    'blog-detail-full-width',
    'blog-right-sidebar',
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
