<?php

namespace App\Services;

use App\Models\IpBlacklist;
use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

class CacheManagementService
{
    /**
     * Gather overall cache driver and optimization status information.
     *
     * @return array<string, mixed>
     */
    public function getOverview(): array
    {
        $driver = (string) config('cache.default', 'database');
        $cacheTable = (string) config('cache.stores.database.table', 'cache');
        $compiledViewsPath = storage_path('framework/views');
        $compiledViewsCount = File::isDirectory($compiledViewsPath)
            ? count(File::glob($compiledViewsPath.'/*.php'))
            : 0;

        $dbCacheCount = null;
        if (Schema::hasTable($cacheTable)) {
            $dbCacheCount = DB::table($cacheTable)->count();
        }

        return [
            'driver' => $driver,
            'prefix' => (string) config('cache.prefix', ''),
            'config_cached' => app()->configurationIsCached(),
            'routes_cached' => app()->routesAreCached(),
            'events_cached' => app()->eventsAreCached(),
            'compiled_views_count' => $compiledViewsCount,
            'db_cache_table' => $cacheTable,
            'db_cache_exists' => Schema::hasTable($cacheTable),
            'db_cache_count' => $dbCacheCount,
        ];
    }

    /**
     * Return known Stisla application cache groups and their current status.
     *
     * @return list<array{key: string, label: string, description: string, is_cached: bool}>
     */
    public function getStislaCaches(): array
    {
        $menuApp = (string) config('stisla.menu_app', 'online-shop');
        $menuKey = "stisla.menus.{$menuApp}";
        $permissionKey = (string) config('permission.cache.key', 'spatie.permission.cache');

        return [
            [
                'key' => $menuKey,
                'label' => __('Sidebar Menu Cache'),
                'description' => __('Cached sidebar navigation tree for active app (:app).', ['app' => $menuApp]),
                'is_cached' => Cache::has($menuKey),
            ],
            [
                'key' => 'stisla.settings.all',
                'label' => __('Application Settings Cache'),
                'description' => __('All key-value settings loaded by Setting::getValue() and Setting::valuesForGroup().'),
                'is_cached' => Cache::has('stisla.settings.all'),
            ],
            [
                'key' => 'stisla.ip_blacklists.active',
                'label' => __('IP Blacklist Cache'),
                'description' => __('Active IP blacklist entries checked on incoming web requests.'),
                'is_cached' => Cache::has('stisla.ip_blacklists.active'),
            ],
            [
                'key' => $permissionKey,
                'label' => __('Role & Permission Cache'),
                'description' => __('Spatie permission and role authorization cache.'),
                'is_cached' => Cache::has($permissionKey),
            ],
        ];
    }

    /**
     * Paginate entries from the database cache table when available.
     *
     * @return LengthAwarePaginator<int, object>
     */
    public function paginateDatabaseCacheEntries(string $search = '', int $perPage = 15): LengthAwarePaginator
    {
        $cacheTable = (string) config('cache.stores.database.table', 'cache');

        if (! Schema::hasTable($cacheTable)) {
            return new Paginator([], 0, $perPage);
        }

        $query = DB::table($cacheTable)->select(['key', 'expiration']);

        if (trim($search) !== '') {
            $query->where('key', 'like', '%'.trim($search).'%');
        }

        $paginator = $query
            ->orderByDesc('expiration')
            ->paginate($perPage)
            ->withQueryString();

        $paginator->setCollection(
            $paginator->getCollection()->map(function (object $row): object {
                $exp = (int) $row->expiration;
                $expiresAt = Carbon::createFromTimestamp($exp);

                return (object) [
                    'key' => (string) $row->key,
                    'expiration' => $exp,
                    'expires_at' => $expiresAt->format('d M Y, H:i:s'),
                    'expires_human' => $expiresAt->diffForHumans(),
                    'is_expired' => $expiresAt->isPast(),
                ];
            })
        );

        return $paginator;
    }

    /**
     * Run a supported cache management action.
     */
    public function runAction(string $action): string
    {
        return match ($action) {
            'cache_clear' => $this->artisanAndReturn('cache:clear', __('Application cache flushed successfully.')),
            'view_clear' => $this->artisanAndReturn('view:clear', __('Compiled Blade views cleared successfully.')),
            'config_clear' => $this->artisanAndReturn('config:clear', __('Configuration cache cleared successfully.')),
            'config_cache' => $this->artisanAndReturn('config:cache', __('Configuration cache rebuilt successfully.')),
            'route_clear' => $this->artisanAndReturn('route:clear', __('Route cache cleared successfully.')),
            'route_cache' => $this->artisanAndReturn('route:cache', __('Route cache rebuilt successfully.')),
            'event_clear' => $this->artisanAndReturn('event:clear', __('Event cache cleared successfully.')),
            'optimize_clear' => $this->artisanAndReturn('optimize:clear', __('All framework caches cleared (optimize:clear).')),
            'permission_reset' => $this->resetPermissionCache(),
            'stisla_flush' => $this->flushStislaCaches(),
            default => throw new \InvalidArgumentException(__('Unsupported cache action.')),
        };
    }

    /**
     * Forget a specific cache key (either via Laravel Cache facade or raw database cache table key).
     */
    public function forgetKey(string $key, bool $isRawDatabaseKey = false): bool
    {
        if ($isRawDatabaseKey) {
            $cacheTable = (string) config('cache.stores.database.table', 'cache');
            if (! Schema::hasTable($cacheTable)) {
                return false;
            }

            return DB::table($cacheTable)->where('key', $key)->delete() > 0;
        }

        if ($key === 'stisla.settings.all') {
            Setting::flushCache();

            return true;
        }

        if ($key === 'stisla.ip_blacklists.active') {
            IpBlacklist::flushCache();

            return true;
        }

        if (str_starts_with($key, 'stisla.menus.')) {
            Menu::flushSidebarCache();

            return true;
        }

        return Cache::forget($key);
    }

    private function artisanAndReturn(string $command, string $message): string
    {
        Artisan::call($command);

        return $message;
    }

    private function resetPermissionCache(): string
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return __('Role and permission cache reset successfully.');
    }

    private function flushStislaCaches(): string
    {
        Menu::flushSidebarCache();
        Setting::flushCache();
        IpBlacklist::flushCache();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return __('All Stisla application caches flushed successfully.');
    }
}
