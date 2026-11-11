<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'vendor_id',
        'size_id',
        'color_id',
        'title',
        'slug',
        'sku',
        'price',
        'discount_percent',
        'stock',
        'short_description',
        'description_detail',
        'spesifications',
        'tags',
        'thumbnail',
        'is_active',
        'is_trending',
        'is_new',
        'is_sale',
        'is_deal',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'stock' => 'integer',
            'spesifications' => 'array',
            'tags' => 'array',
            'is_active' => 'boolean',
            'is_trending' => 'boolean',
            'is_new' => 'boolean',
            'is_sale' => 'boolean',
            'is_deal' => 'boolean',
        ];
    }

    /**
     * Category relation.
     *
     * @return BelongsTo<ProductCategory, Product>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Brand relation.
     *
     * @return BelongsTo<ProductBrand, Product>
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }

    /**
     * Vendor relation.
     *
     * @return BelongsTo<ProductVendor, Product>
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(ProductVendor::class, 'vendor_id');
    }

    /**
     * Size relation.
     *
     * @return BelongsTo<ProductSize, Product>
     */
    public function size(): BelongsTo
    {
        return $this->belongsTo(ProductSize::class, 'size_id');
    }

    /**
     * Color relation.
     *
     * @return BelongsTo<ProductColor, Product>
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(ProductColor::class, 'color_id');
    }

    /**
     * Gallery images relation.
     *
     * @return HasMany<ProductImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    /**
     * Product reviews relation.
     *
     * @return HasMany<ProductReview, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->latest('id');
    }

    /**
     * Approved product reviews relation.
     *
     * @return HasMany<ProductReview, $this>
     */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true)->latest('id');
    }

    /**
     * Average rating accessor.
     */
    protected function averageRating(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (array_key_exists('approved_reviews_avg_rating', $this->attributes)) {
                    return round((float) ($this->attributes['approved_reviews_avg_rating'] ?? 0), 1);
                }

                return round((float) ($this->approvedReviews()->avg('rating') ?? 0), 1);
            }
        );
    }

    /**
     * Cart items relation.
     *
     * @return HasMany<ProductCart, $this>
     */
    public function carts(): HasMany
    {
        return $this->hasMany(ProductCart::class);
    }

    /**
     * Wishlist entries relation.
     *
     * @return HasMany<ProductWishlist, $this>
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(ProductWishlist::class);
    }

    /**
     * Get final price after discount.
     */
    public function getFinalPriceAttribute(): float
    {
        $discount = (float) $this->discount_percent;
        $price = (float) $this->price;

        if ($discount > 0) {
            return round($price - ($price * ($discount / 100)), 2);
        }

        return $price;
    }

    /**
     * Thumbnail URL accessor with robust fallback.
     */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $fallback = asset('assets/img/products/product-1-50.png');
                if (file_exists(public_path('mantu/assets/img/product/1.jpg'))) {
                    $fallback = asset('mantu/assets/img/product/1.jpg');
                }

                if (empty($this->thumbnail)) {
                    return $fallback;
                }

                if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
                    return $this->thumbnail;
                }

                if (file_exists(public_path($this->thumbnail))) {
                    return asset($this->thumbnail);
                }

                // If relative to storage
                if (file_exists(storage_path('app/public/'.$this->thumbnail))) {
                    return asset('storage/'.$this->thumbnail);
                }

                // Fix plural path mantu/assets/img/products/ -> product/
                $fixed = str_replace('mantu/assets/img/products/', 'mantu/assets/img/product/', $this->thumbnail);
                if (file_exists(public_path($fixed))) {
                    return asset($fixed);
                }

                return $fallback;
            }
        );
    }

    /**
     * Scope active products.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope trending products.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeTrending(Builder $query): Builder
    {
        return $query->where('is_trending', true);
    }

    /**
     * Scope new arrivals products.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeNew(Builder $query): Builder
    {
        return $query->where('is_new', true);
    }

    /**
     * Scope on sale products.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeSale(Builder $query): Builder
    {
        return $query->where('is_sale', true);
    }

    /**
     * Scope deal products.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeDeal(Builder $query): Builder
    {
        return $query->where('is_deal', true);
    }

    /**
     * Generate unique SKU.
     */
    public static function generateUniqueSku(string $prefix = 'PRD'): string
    {
        do {
            $sku = strtoupper($prefix.'-'.date('Y').'-'.Str::random(5));
        } while (static::query()->where('sku', $sku)->exists());

        return $sku;
    }

    /**
     * Generate unique slug.
     */
    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        if (empty($baseSlug)) {
            $baseSlug = 'product';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
