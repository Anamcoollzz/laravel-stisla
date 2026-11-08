<?php

namespace App\Models;

use Database\Factories\MenuFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

#[Fillable(['parent_id', 'name', 'type', 'icon', 'route', 'url', 'permission', 'order', 'is_active'])]
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
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
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
            $baseRoute = explode('.', $this->route)[0];
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
