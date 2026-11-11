<?php

namespace App\Providers;

use App\Models\ChatMessage;
use App\Models\Menu;
use App\Models\Product;
use App\Models\ProductBanner;
use App\Models\ProductCart;
use App\Models\ProductCategory;
use App\Models\ProductWishlist;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        View::composer('layouts.app', function ($view) {
            $authUser = auth()->user();

            if ($authUser === null || ! Schema::hasTable('chat_messages')) {
                $view->with('navMessages', collect())->with('navUnreadCount', 0);

                return;
            }

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
        });

        View::composer(['layouts.app', 'partials.sidebar-pages'], function ($view) {
            if (Schema::hasTable('menus')) {
                $sidebarMenus = Menu::query()
                    ->with(['children' => fn ($query) => $query->where('is_active', true)->orderBy('order')])
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

            if (Schema::hasTable('products')) {
                $randomProducts = Product::query()
                    ->active()
                    ->with(['category', 'brand'])
                    ->inRandomOrder()
                    ->take(4)
                    ->get();

                $view->with('randomProducts', $randomProducts);
            } else {
                $view->with('randomProducts', collect());
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

            $view->with('layoutCart', $layoutCart)
                ->with('layoutCartSubtotal', $layoutCartSubtotal)
                ->with('cartCount', $cartCount)
                ->with('layoutWishlistProducts', $layoutWishlistProducts)
                ->with('userWishlistIds', $userWishlistIds)
                ->with('wishlistCount', $wishlistCount);
        });

        View::composer('ecommerce.pages.index', function ($view) {
            if (Schema::hasTable('product_banners')) {
                $heroBanners = ProductBanner::query()
                    ->active()
                    ->hero()
                    ->orderBy('order')
                    ->latest('id')
                    ->get();

                $view->with('heroBanners', $heroBanners);
            } else {
                $view->with('heroBanners', collect());
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
            } else {
                $view->with('newArrivalProducts', collect())
                    ->with('dealProducts', collect());
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
        });
    }
}
