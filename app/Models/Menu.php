<?php

namespace App\Models;

use App\Observers\MenuObserver;
use Database\Factories\MenuFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

#[Fillable(['app', 'parent_id', 'name', 'type', 'icon', 'route', 'url', 'permission', 'order', 'is_active'])]
#[ObservedBy([MenuObserver::class])]
class Menu extends Model
{
    /** @use HasFactory<MenuFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'app' => 'array',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $menu): void {
            if (blank($menu->app)) {
                $menu->app = ['online-shop'];
            }
        });
    }

    /**
     * Scope menus assigned to the given app identifier.
     */
    public function scopeForApp(Builder $query, string $app): Builder
    {
        return $query->where(function (Builder $q) use ($app): void {
            $q->whereJsonContains('app', $app)
                ->orWhereNull('app');
        });
    }

    /**
     * Collect all distinct app identifiers across menus.
     *
     * @return Collection<int, string>
     */
    public static function distinctApps(): Collection
    {
        return static::query()
            ->pluck('app')
            ->flatten()
            ->merge(['online-shop', 'spp-payment', 'news', 'lp-compro-workers', 'nikahku'])
            ->map(fn (mixed $item): string => trim((string) $item))
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * In-memory request-level cache keyed by app identifier.
     *
     * @var array<string, Collection<int, self>>
     */
    protected static array $memoizedSidebars = [];

    /**
     * Retrieve the cached sidebar menu tree for the given app identifier.
     *
     * @return Collection<int, self>
     */
    public static function sidebarForApp(string $menuApp): Collection
    {
        if (isset(static::$memoizedSidebars[$menuApp])) {
            return static::$memoizedSidebars[$menuApp];
        }

        $cached = Cache::get("stisla.menus.{$menuApp}");

        if (! is_array($cached)) {
            Cache::forget("stisla.menus.{$menuApp}");

            $cached = Cache::rememberForever("stisla.menus.{$menuApp}", function () use ($menuApp): array {
                return static::query()
                    ->with(['children' => fn ($query) => $query->where('is_active', true)->forApp($menuApp)->orderBy('order')])
                    ->forApp($menuApp)
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->get()
                    ->map(function (self $menu): array {
                        $attributes = $menu->getAttributes();
                        $attributes['children'] = $menu->children
                            ->map(fn (self $child): array => $child->getAttributes())
                            ->values()
                            ->all();

                        return $attributes;
                    })
                    ->values()
                    ->all();
            });
        }

        $menus = collect($cached)->map(function (array $row): self {
            $childrenRows = is_array($row['children'] ?? null) ? $row['children'] : [];
            unset($row['children']);

            /** @var self $menu */
            $menu = (new static)->newFromBuilder($row);
            $children = collect($childrenRows)->map(fn (array $childRow): self => (new static)->newFromBuilder($childRow));
            $menu->setRelation('children', $children);

            return $menu;
        });

        return static::$memoizedSidebars[$menuApp] = $menus;
    }

    /**
     * Flush cached sidebar menus for all known and affected app identifiers.
     *
     * @param  array<int, mixed>|null  $extraApps
     */
    public static function flushSidebarCache(?array $extraApps = null): void
    {
        static::$memoizedSidebars = [];

        $apps = collect(['online-shop', 'spp-payment', 'news', 'lp-compro-workers', (string) config('stisla.menu_app', 'online-shop')])
            ->merge($extraApps ?? []);

        if (Schema::hasTable('menus')) {
            $apps = $apps->merge(static::distinctApps());
        }

        $apps->map(fn (mixed $app): string => trim((string) $app))
            ->filter()
            ->unique()
            ->each(fn (string $app) => Cache::forget("stisla.menus.{$app}"));
    }

    /**
     * @return BelongsTo<Menu, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * @return HasMany<Menu, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    /**
     * Resolve the target URL for the menu item.
     */
    public function resolvedUrl(): string
    {
        if ($this->route && Route::has($this->route)) {
            return route($this->route);
        }

        return $this->url ?: '#';
    }

    /**
     * Determine if this menu or its children match the current route.
     */
    public function isActiveRoute(): bool
    {
        if ($this->route) {
            $segments = explode('.', $this->route);
            $baseRoute = count($segments) > 2
                ? implode('.', array_slice($segments, 0, -1))
                : $segments[0];

            if (request()->routeIs($this->route) || request()->routeIs("{$baseRoute}.*")) {
                return true;
            }
        }

        if ($this->children->isNotEmpty()) {
            foreach ($this->children as $child) {
                if ($child->isActiveRoute()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if a user can access this menu item.
     */
    public function canAccess(?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user) {
            return false;
        }

        // If user is admin, allow access by default
        if ($user->hasRole('admin')) {
            return true;
        }

        // For dropdown, check if user can access at least one child
        if ($this->type === 'dropdown') {
            if ($this->permission && ! $user->can($this->permission)) {
                return false;
            }

            if ($this->children->isEmpty()) {
                return true;
            }

            return $this->children->contains(fn (Menu $child) => $child->canAccess($user));
        }

        // For headers and standard items, check permission if specified
        if ($this->permission) {
            return $user->can($this->permission);
        }

        return true;
    }

    /**
     * Badge class for menu type.
     */
    public function typeBadgeClass(): string
    {
        return match ($this->type) {
            'header' => 'badge-dark',
            'dropdown' => 'badge-primary',
            'item' => 'badge-info',
            default => 'badge-secondary',
        };
    }
}
