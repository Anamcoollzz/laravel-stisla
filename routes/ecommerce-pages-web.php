<?php

use App\Http\Controllers\BlogCommentController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\DashboardController;
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
use App\Http\Controllers\EcommerceFaqController;
use App\Http\Controllers\FooterProductCategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentBankController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductBannerController;
use App\Http\Controllers\ProductBrandController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductColorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProductSizeController;
use App\Http\Controllers\ProductVendorController;
use App\Http\Controllers\ShippingMethodController;
use App\Http\Controllers\StoreServiceController;
use App\Http\Controllers\TestimonialController;
use App\Models\Blog;
use App\Models\EcommerceFaq;
use App\Models\OurTeam;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductVendor;
use App\Models\Setting;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
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
    $openTicket = Auth::check()
        ? Ticket::query()
            ->where('user_id', (int) Auth::id())
            ->where('status', '!=', 'closed')
            ->latest('id')
            ->first()
        : null;

    return view('ecommerce.pages.contact-us', compact('siteContact', 'openTicket'));
};

Route::get('/ecommerce/contact-us', $contactUsHandler)->name('ecommerce.contact-us');
Route::post('/ecommerce/contact-us', [EcommerceTicketController::class, 'storeFromContact'])
    ->name('ecommerce.contact-us.submit');
Route::get('/ecommerce/ticket-list', [EcommerceTicketController::class, 'index'])
    ->name('ecommerce.ticket-list');
Route::post('/ecommerce/ticket-list/{ticket}/reply', [EcommerceTicketController::class, 'reply'])
    ->name('ecommerce.ticket-list.reply');
Route::patch('/ecommerce/ticket-list/{ticket}/close', [EcommerceTicketController::class, 'close'])
    ->name('ecommerce.ticket-list.close');
Route::get('/ecommerce-pages/contact-us.html', $contactUsHandler)->name('ecommerce.contact-us.html');
Route::get('/ecommerce-pages/contact-us', $contactUsHandler);

$termsHandler = function () {
    $termsContent = Setting::getValue('terms_content') ?: config('settings.groups.terms.fields.terms_content.default');

    return view('ecommerce.pages.terms', compact('termsContent'));
};

Route::get('/ecommerce/terms', $termsHandler)->name('ecommerce.terms');
Route::get('/ecommerce-pages/terms.html', $termsHandler)->name('ecommerce.terms.html');
Route::get('/ecommerce-pages/terms', $termsHandler);

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
    'track-order',
];

foreach ($ecommercePages as $page) {
    Route::view("/ecommerce-pages/{$page}.html", "ecommerce.pages.{$page}")
        ->name("ecommerce.{$page}.html");
    Route::view("/ecommerce-pages/{$page}", "ecommerce.pages.{$page}")
        ->name("ecommerce.{$page}");
}

Route::prefix('app')->middleware('auth')->group(function () {
    Route::get('/ecommerce/dashboard', [DashboardController::class, 'ecommerce'])->name('dashboard.ecommerce');

    Route::post('product-categories/{productCategory}/duplicate', [ProductCategoryController::class, 'duplicate'])->name('product-categories.duplicate');
    Route::get('product-categories/export', [ProductCategoryController::class, 'export'])->name('product-categories.export');
    Route::get('product-categories/export-pdf', [ProductCategoryController::class, 'exportPdf'])->name('product-categories.exportPdf');
    Route::get('product-categories/template', [ProductCategoryController::class, 'template'])->name('product-categories.template');
    Route::post('product-categories/import', [ProductCategoryController::class, 'import'])->name('product-categories.import');
    Route::resource('product-categories', ProductCategoryController::class)->parameters([
        'product-categories' => 'productCategory',
    ]);

    Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::get('products/export-pdf', [ProductController::class, 'exportPdf'])->name('products.exportPdf');
    Route::get('products/template', [ProductController::class, 'template'])->name('products.template');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    Route::resource('products', ProductController::class);

    Route::post('product-brands/{productBrand}/duplicate', [ProductBrandController::class, 'duplicate'])->name('product-brands.duplicate');
    Route::get('product-brands/export', [ProductBrandController::class, 'export'])->name('product-brands.export');
    Route::get('product-brands/export-pdf', [ProductBrandController::class, 'exportPdf'])->name('product-brands.exportPdf');
    Route::get('product-brands/template', [ProductBrandController::class, 'template'])->name('product-brands.template');
    Route::post('product-brands/import', [ProductBrandController::class, 'import'])->name('product-brands.import');
    Route::resource('product-brands', ProductBrandController::class)->parameters([
        'product-brands' => 'productBrand',
    ]);

    Route::post('product-vendors/{productVendor}/duplicate', [ProductVendorController::class, 'duplicate'])->name('product-vendors.duplicate');
    Route::get('product-vendors/export', [ProductVendorController::class, 'export'])->name('product-vendors.export');
    Route::get('product-vendors/export-pdf', [ProductVendorController::class, 'exportPdf'])->name('product-vendors.exportPdf');
    Route::get('product-vendors/template', [ProductVendorController::class, 'template'])->name('product-vendors.template');
    Route::post('product-vendors/import', [ProductVendorController::class, 'import'])->name('product-vendors.import');
    Route::resource('product-vendors', ProductVendorController::class)->parameters([
        'product-vendors' => 'productVendor',
    ]);

    Route::post('product-sizes/{productSize}/duplicate', [ProductSizeController::class, 'duplicate'])->name('product-sizes.duplicate');
    Route::get('product-sizes/export', [ProductSizeController::class, 'export'])->name('product-sizes.export');
    Route::get('product-sizes/export-pdf', [ProductSizeController::class, 'exportPdf'])->name('product-sizes.exportPdf');
    Route::get('product-sizes/template', [ProductSizeController::class, 'template'])->name('product-sizes.template');
    Route::post('product-sizes/import', [ProductSizeController::class, 'import'])->name('product-sizes.import');
    Route::resource('product-sizes', ProductSizeController::class)->parameters([
        'product-sizes' => 'productSize',
    ]);

    Route::post('product-colors/{productColor}/duplicate', [ProductColorController::class, 'duplicate'])->name('product-colors.duplicate');
    Route::get('product-colors/export', [ProductColorController::class, 'export'])->name('product-colors.export');
    Route::get('product-colors/export-pdf', [ProductColorController::class, 'exportPdf'])->name('product-colors.exportPdf');
    Route::get('product-colors/template', [ProductColorController::class, 'template'])->name('product-colors.template');
    Route::post('product-colors/import', [ProductColorController::class, 'import'])->name('product-colors.import');
    Route::resource('product-colors', ProductColorController::class)->parameters([
        'product-colors' => 'productColor',
    ]);

    Route::post('product-banners/{productBanner}/duplicate', [ProductBannerController::class, 'duplicate'])->name('product-banners.duplicate');
    Route::get('product-banners/export', [ProductBannerController::class, 'export'])->name('product-banners.export');
    Route::get('product-banners/export-pdf', [ProductBannerController::class, 'exportPdf'])->name('product-banners.exportPdf');
    Route::get('product-banners/template', [ProductBannerController::class, 'template'])->name('product-banners.template');
    Route::post('product-banners/import', [ProductBannerController::class, 'import'])->name('product-banners.import');
    Route::resource('product-banners', ProductBannerController::class)->parameters([
        'product-banners' => 'productBanner',
    ]);

    Route::post('blogs/{blog}/duplicate', [BlogController::class, 'duplicate'])->name('blogs.duplicate');
    Route::resource('blogs', BlogController::class);

    Route::patch('blog-comments/{blogComment}/toggle-approval', [BlogCommentController::class, 'toggleApproval'])->name('blog-comments.toggleApproval');
    Route::resource('blog-comments', BlogCommentController::class)->only(['index', 'show', 'destroy'])->parameters([
        'blog-comments' => 'blogComment',
    ]);

    Route::post('testimonials/{testimonial}/duplicate', [TestimonialController::class, 'duplicate'])->name('testimonials.duplicate');
    Route::resource('testimonials', TestimonialController::class);

    Route::patch('product-reviews/{productReview}/toggle-approval', [ProductReviewController::class, 'toggleApproval'])->name('product-reviews.toggleApproval');
    Route::resource('product-reviews', ProductReviewController::class)->only(['index', 'show', 'destroy'])->parameters([
        'product-reviews' => 'productReview',
    ]);

    Route::resource('store-services', StoreServiceController::class)->parameters([
        'store-services' => 'storeService',
    ]);

    Route::post('ecommerce-faqs/{ecommerceFaq}/duplicate', [EcommerceFaqController::class, 'duplicate'])->name('ecommerce-faqs.duplicate');
    Route::get('ecommerce-faqs/export', [EcommerceFaqController::class, 'export'])->name('ecommerce-faqs.export');
    Route::get('ecommerce-faqs/template', [EcommerceFaqController::class, 'template'])->name('ecommerce-faqs.template');
    Route::post('ecommerce-faqs/import', [EcommerceFaqController::class, 'import'])->name('ecommerce-faqs.import');
    Route::resource('ecommerce-faqs', EcommerceFaqController::class)->parameters([
        'ecommerce-faqs' => 'ecommerceFaq',
    ]);

    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.updatePaymentStatus');
    Route::patch('orders/{order}/tracking', [OrderController::class, 'updateTracking'])->name('orders.updateTracking');
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'destroy']);

    Route::resource('footer-product-categories', FooterProductCategoryController::class)->parameters([
        'footer-product-categories' => 'footerProductCategory',
    ]);

    Route::patch('shipping-methods/{shippingMethod}/toggle', [ShippingMethodController::class, 'toggle'])->name('shipping-methods.toggle');
    Route::resource('shipping-methods', ShippingMethodController::class)->parameters([
        'shipping-methods' => 'shippingMethod',
    ]);

    Route::patch('payment-methods/{paymentMethod}/toggle', [PaymentMethodController::class, 'toggle'])->name('payment-methods.toggle');
    Route::resource('payment-methods', PaymentMethodController::class)->parameters([
        'payment-methods' => 'paymentMethod',
    ]);

    Route::patch('payment-banks/{paymentBank}/toggle', [PaymentBankController::class, 'toggle'])->name('payment-banks.toggle');
    Route::resource('payment-banks', PaymentBankController::class)->parameters([
        'payment-banks' => 'paymentBank',
    ]);
});
