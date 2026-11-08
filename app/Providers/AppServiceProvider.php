<?php

namespace App\Providers;

use App\Models\ChatMessage;
use App\Models\Menu;
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
    }
}
