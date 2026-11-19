<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\AppNotification;
use App\Models\ChatMessage;
use App\Models\FooterProductCategory;
use App\Models\LP\CompanyProfile;
use App\Models\Menu;
use App\Models\News\News;
use App\Models\PaymentBank;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductBanner;
use App\Models\ProductCart;
use App\Models\ProductCategory;
use App\Models\ProductWishlist;
use App\Models\Setting;
use App\Models\StoreService;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::useBuildDirectory('lp-build');
        Blade::anonymousComponentPath(resource_path('views/lp/components'));

        if (Schema::hasTable('company_profiles')) {
            $companyProfile = CompanyProfile::query()->first();

            if ($companyProfile) {
                config([
                    'company.name' => $companyProfile->name,
                    'company.legal_name' => $companyProfile->legal_name,
                    'company.tagline' => $companyProfile->tagline ?: config('company.tagline'),
                    'company.footer_description' => $companyProfile->footer_description ?: config('company.footer_description'),
                    'company.license_number' => $companyProfile->license_number ?: config('company.license_number'),
                    'company.whatsapp_number' => $companyProfile->whatsapp_number,
                    'company.phone_display' => $companyProfile->phone_display,
                    'company.secondary_phone' => $companyProfile->secondary_phone ?: config('company.secondary_phone'),
                    'company.email' => $companyProfile->email,
                    'company.address' => $companyProfile->address,
                    'company.short_address' => $companyProfile->short_address ?: config('company.short_address'),
                    'company.operating_hours.weekday' => $companyProfile->operating_hours_weekday ?: config('company.operating_hours.weekday'),
                    'company.operating_hours.saturday' => $companyProfile->operating_hours_saturday ?: config('company.operating_hours.saturday'),
                    'company.operating_hours.sunday' => $companyProfile->operating_hours_sunday ?: config('company.operating_hours.sunday'),
                    'company.map_embed_url' => $companyProfile->map_embed_url ?: config('company.map_embed_url'),
                    'company.social_links.instagram' => $companyProfile->instagram_url ?: config('company.social_links.instagram'),
                    'company.social_links.facebook' => $companyProfile->facebook_url ?: config('company.social_links.facebook'),
                    'company.social_links.youtube' => $companyProfile->youtube_url ?: config('company.social_links.youtube'),
                    'company.default_whatsapp_message' => $companyProfile->default_whatsapp_message ?: config('company.default_whatsapp_message'),
                ]);

                View::share('lpCompanyProfile', $companyProfile);
            }
        }

        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        Event::listen(Login::class, function (Login $event): void {
            if ($event->user instanceof User) {
                $event->user->forceFill(['last_logged_in' => now()])->saveQuietly();
            }
        });

        Event::listen(Login::class, function (Login $event): void {
            if ($event->user instanceof User) {
                $event->user->forceFill(['last_logged_in' => now()])->saveQuietly();
            }
        });

        View::composer('layouts.app', function ($view) {
            $authUser = auth()->user();

            if ($authUser instanceof User && $authUser->last_logged_in === null) {
                $authUser->forceFill(['last_logged_in' => now()])->saveQuietly();
            }

            if ($authUser instanceof User && $authUser->last_logged_in === null) {
                $authUser->forceFill(['last_logged_in' => now()])->saveQuietly();
            }

            if ($authUser === null || ! Schema::hasTable('chat_messages')) {
                $view->with('navMessages', collect())->with('navUnreadCount', 0);
            } else {
                $navMessages = ChatMessage::query()
                    ->where('receiver_id', $authUser->id)
                    ->with('sender')
                    ->latest('id')
                    ->limit(30)
                    ->get()
                    ->unique('sender_id')
                    ->take(5)
                    ->values();

                $view->with('navMessages', $navMessages)->with(
                    'navUnreadCount',
                    ChatMessage::query()->where('receiver_id', $authUser->id)->where('is_read', false)->count()
                );
            }

            if ($authUser === null || ! Schema::hasTable('app_notifications')) {
                $view->with('navNotifications', collect())->with('navNotificationUnreadCount', 0);
            } else {
                $navNotifications = AppNotification::query()
                    ->forUser($authUser->id)
                    ->with(['readers' => fn ($query) => $query->where('users.id', $authUser->id)])
                    ->latest('id')
                    ->limit(10)
                    ->get();

                $navNotificationUnreadCount = AppNotification::query()
                    ->forUser($authUser->id)
                    ->whereDoesntHave('readers', fn ($query) => $query->where('users.id', $authUser->id))
                    ->count();

                $view->with('navNotifications', $navNotifications)
                    ->with('navNotificationUnreadCount', $navNotificationUnreadCount);
            }
        });

        View::composer(['dashboard', 'dashboard-ecommerce', 'dashboards.compro-workers', 'dashboards.user'], function ($view) {
            $announcements = Schema::hasTable('announcements')
                ? Announcement::query()->published()->limit(5)->get()
                : collect();

            $view->with('dashboardAnnouncements', $announcements);
        });

        View::composer(['layouts.app', 'partials.sidebar-pages'], function ($view) {
            if (Schema::hasTable('menus')) {
                $menuApp = (string) config('stisla.menu_app', 'online-shop');
                $sidebarMenus = Menu::query()
                    ->with(['children' => fn ($query) => $query->where('is_active', true)->forApp($menuApp)->orderBy('order')])
                    ->forApp($menuApp)
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->get();

                $view->with('sidebarMenus', $sidebarMenus);
            }
        });

        View::composer('ecommerce.layouts.app-ecommerce', function ($view) {
            if (Schema::hasTable('product_categories')) {
                $ecommerceCategories = ProductCategory::query()
                    ->with([
                        'children' => fn ($q) => $q->where('is_active', true)->orderBy('order')->with([
                            'children' => fn ($subQ) => $subQ->where('is_active', true)->orderBy('order'),
                        ]),
                    ])
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->get()
                    ->unique('name')
                    ->values();

                $view->with('ecommerceCategories', $ecommerceCategories);
            } else {
                $view->with('ecommerceCategories', collect());
            }

            if (Schema::hasTable('footer_product_categories')) {
                $footerCategories = FooterProductCategory::query()
                    ->active()
                    ->whereHas('category', fn ($q) => $q->where('is_active', true))
                    ->with('category')
                    ->orderBy('order')
                    ->latest('id')
                    ->get();

                $view->with('footerCategories', $footerCategories);
            } else {
                $view->with('footerCategories', collect());
            }

            if (Schema::hasTable('products')) {
                $randomProducts = Product::query()
                    ->active()
                    ->with(['category', 'brand'])
                    ->inRandomOrder()
                    ->take(4)
                    ->get();

                $view->with('randomProducts', $randomProducts);

                $bestSellerProducts = Product::query()
                    ->active()
                    ->when(Schema::hasTable('order_items'), fn ($q) => $q->withSum('orderItems as total_sold', 'quantity')->orderByDesc('total_sold'))
                    ->orderByDesc('is_trending')
                    ->latest('id')
                    ->take(6)
                    ->get();

                $view->with('bestSellerProducts', $bestSellerProducts);
            } else {
                $view->with('randomProducts', collect())
                    ->with('bestSellerProducts', collect());
            }

            $authUser = auth()->user();
            if ($authUser !== null && Schema::hasTable('product_carts') && Schema::hasTable('product_wishlists')) {
                $cartRows = ProductCart::query()
                    ->where('user_id', $authUser->id)
                    ->with(['product' => fn ($q) => $q->active()])
                    ->get();

                $layoutCart = [];
                foreach ($cartRows as $row) {
                    if ($p = $row->product) {
                        $layoutCart[$p->id] = [
                            'id' => (int) $p->id,
                            'title' => $p->title,
                            'slug' => $p->slug,
                            'price' => (float) $p->final_price,
                            'original_price' => (float) $p->price,
                            'thumbnail' => $p->thumbnail_url,
                            'quantity' => (int) $row->quantity,
                            'stock' => (int) $p->stock,
                        ];
                    }
                }

                $layoutCartSubtotal = array_sum(array_map(fn ($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1), $layoutCart));
                $cartCount = array_sum(array_column($layoutCart, 'quantity'));

                $layoutWishlistProducts = ProductWishlist::query()
                    ->where('user_id', $authUser->id)
                    ->with(['product' => fn ($q) => $q->active()->with(['category', 'brand', 'size', 'color', 'images'])])
                    ->latest('id')
                    ->get()
                    ->pluck('product')
                    ->filter()
                    ->values();

                $userWishlistIds = $layoutWishlistProducts->pluck('id')->map(fn ($id) => (int) $id)->toArray();
                $wishlistCount = count($userWishlistIds);
            } else {
                $layoutCart = [];
                $layoutCartSubtotal = 0.0;
                $cartCount = 0;
                $layoutWishlistProducts = collect();
                $userWishlistIds = [];
                $wishlistCount = 0;
            }

            $siteContact = Schema::hasTable('settings')
                ? Setting::getContactData()
                : [];

            $storefrontBranding = Schema::hasTable('settings')
                ? Setting::getStorefrontData()
                : [];

            $footerPaymentLogos = collect();
            if (Schema::hasTable('payment_banks')) {
                $footerPaymentLogos = $footerPaymentLogos->merge(
                    PaymentBank::query()
                        ->active()
                        ->whereNotNull('logo')
                        ->where('logo', '!=', '')
                        ->orderBy('order')
                        ->get()
                        ->map(fn (PaymentBank $bank) => [
                            'name' => $bank->bank_name,
                            'logo_url' => $bank->logo_url,
                        ])
                );
            }
            if (Schema::hasTable('payment_methods')) {
                $footerPaymentLogos = $footerPaymentLogos->merge(
                    PaymentMethod::query()
                        ->active()
                        ->whereNotNull('logo')
                        ->where('logo', '!=', '')
                        ->orderBy('order')
                        ->get()
                        ->map(fn (PaymentMethod $method) => [
                            'name' => $method->name,
                            'logo_url' => $method->logo_url,
                        ])
                );
            }

            $view->with('layoutCart', $layoutCart)
                ->with('layoutCartSubtotal', $layoutCartSubtotal)
                ->with('cartCount', $cartCount)
                ->with('layoutWishlistProducts', $layoutWishlistProducts)
                ->with('userWishlistIds', $userWishlistIds)
                ->with('wishlistCount', $wishlistCount)
                ->with('siteContact', $siteContact)
                ->with('storefrontBranding', $storefrontBranding)
                ->with('footerPaymentLogos', $footerPaymentLogos);
        });

        View::composer('ecommerce.pages.index', function ($view) {
            if (Schema::hasTable('product_banners')) {
                $heroBanners = ProductBanner::query()
                    ->active()
                    ->hero()
                    ->with('product')
                    ->orderBy('order')
                    ->latest('id')
                    ->get();

                $modernBanners = ProductBanner::query()
                    ->active()
                    ->modern()
                    ->with('product.category')
                    ->orderBy('order')
                    ->latest('id')
                    ->get();

                if ($modernBanners->isEmpty()) {
                    $modernBanners = $heroBanners;
                }

                $view->with('heroBanners', $heroBanners)
                    ->with('modernBanners', $modernBanners);
            } else {
                $view->with('heroBanners', collect())
                    ->with('modernBanners', collect());
            }

            if (Schema::hasTable('product_categories')) {
                $featuredCategories = ProductCategory::query()
                    ->where('is_active', true)
                    ->whereHas('products', fn ($q) => $q->active())
                    ->with('parent')
                    ->withCount(['products' => fn ($q) => $q->active()])
                    ->withMax(['products' => fn ($q) => $q->active()], 'discount_percent')
                    ->with(['products' => fn ($q) => $q->active()->latest('id')])
                    ->orderByDesc('products_count')
                    ->take(8)
                    ->get()
                    ->each(function ($cat) {
                        $cat->setRelation('products', $cat->products->take(3));
                    });

                $view->with('featuredCategories', $featuredCategories);
            } else {
                $view->with('featuredCategories', collect());
            }

            if (Schema::hasTable('products')) {
                $newArrivalProducts = Product::query()
                    ->active()
                    ->with(['category', 'brand', 'size', 'color'])
                    ->latest('id')
                    ->take(12)
                    ->get();

                $view->with('newArrivalProducts', $newArrivalProducts);

                $dealProducts = Product::query()
                    ->active()
                    ->deal()
                    ->with(['category', 'brand', 'size', 'color'])
                    ->latest('id')
                    ->take(12)
                    ->get();

                if ($dealProducts->isEmpty()) {
                    $dealProducts = Product::query()
                        ->active()
                        ->where('discount_percent', '>', 0)
                        ->with(['category', 'brand', 'size', 'color'])
                        ->latest('id')
                        ->take(12)
                        ->get();
                }

                $view->with('dealProducts', $dealProducts);

                $instaProducts = Product::query()
                    ->active()
                    ->inRandomOrder()
                    ->take(8)
                    ->get();

                $view->with('instaProducts', $instaProducts);
            } else {
                $view->with('newArrivalProducts', collect())
                    ->with('dealProducts', collect())
                    ->with('instaProducts', collect());
            }

            if (Schema::hasTable('testimonials')) {
                $testimonials = Testimonial::query()
                    ->active()
                    ->orderBy('order')
                    ->latest('id')
                    ->get();

                $view->with('testimonials', $testimonials);
            } else {
                $view->with('testimonials', collect());
            }

            if (Schema::hasTable('store_services')) {
                $storeServices = StoreService::query()
                    ->active()
                    ->orderBy('order')
                    ->latest('id')
                    ->get();

                $view->with('storeServices', $storeServices);
            } else {
                $view->with('storeServices', collect());
            }
        });

        View::composer('news.partials.footer', function ($view) {
            if (Schema::hasTable('news')) {
                $popularNewsPosts = News::query()
                    ->published()
                    ->with(['publisher'])
                    ->orderByDesc('views_count')
                    ->orderByDesc('published_at')
                    ->latest('id')
                    ->limit(3)
                    ->get();

                $view->with('popularNewsPosts', $popularNewsPosts);
            } else {
                $view->with('popularNewsPosts', collect());
            }
        });
    }
}
