<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

#[Fillable(['parent_id', 'name', 'slug', 'icon', 'route', 'url', 'description', 'order', 'is_active'])]
class ProductCategory extends Model
{
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
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (ProductCategory $category) {
            if (empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name);
            }
        });
    }

    /**
     * Generate a unique slug from a name.
     */
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $count = 1;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$base}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * @return BelongsTo<ProductCategory, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * @return HasMany<ProductCategory, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')->orderBy('order');
    }

    /**
     * Products belonging to this category.
     *
     * @return HasMany<Product, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * Determine category level (1, 2, or 3).
     */
    public function getLevelAttribute(): int
    {
        if (! $this->parent_id) {
            return 1;
        }

        return $this->parent?->parent_id ? 3 : 2;
    }

    /**
     * Determine if this category acts as a dropdown (has children).
     */
    public function isDropdown(): bool
    {
        return $this->children->isNotEmpty();
    }

    /**
     * Resolve target URL.
     */
    public function resolvedUrl(): string
    {
        if ($this->route && Route::has($this->route)) {
            return route($this->route);
        }

        return $this->url ?: '#';
    }

    /**
     * Get icon URL (supports mantu/ asset or uploaded storage).
     */
    public function getIconUrlAttribute(): ?string
    {
        if (! $this->icon) {
            return null;
        }

        if (str_starts_with($this->icon, 'http://') || str_starts_with($this->icon, 'https://')) {
            return $this->icon;
        }

        if (str_starts_with($this->icon, 'mantu/') || str_starts_with($this->icon, 'assets/')) {
            $path = str_starts_with($this->icon, 'mantu/') ? $this->icon : 'mantu/'.$this->icon;

            return asset($path);
        }

        return asset('storage/'.$this->icon);
    }
}
