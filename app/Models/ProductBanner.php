<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'badge_label',
        'button_text',
        'button_url',
        'image',
        'position',
        'product_id',
        'order',
        'is_active',
    ];

    /**
     * Product linked to this banner.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Resolved button URL prioritizing linked product detail.
     */
    public function getTargetUrlAttribute(): string
    {
        if ($this->product) {
            return route('ecommerce.products.detail', $this->product->slug);
        }

        if ($this->button_url && $this->button_url !== '#' && $this->button_url !== '/ecommerce-pages/shop-left-sidebar.html') {
            return $this->button_url;
        }

        return route('ecommerce.products.index');
    }

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get resolved image URL with fallback to template hero assets.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }

            if (Storage::disk('public')->exists($this->image)) {
                return Storage::disk('public')->url($this->image);
            }

            if (file_exists(public_path($this->image))) {
                return asset($this->image);
            }
        }

        $fallbackIndex = (($this->order ?: $this->id ?: 1) % 4) ?: 4;

        return asset("mantu/assets/img/hero/{$fallbackIndex}.jpg");
    }

    /**
     * Scope active banners.
     *
     * @param  Builder<ProductBanner>  $query
     * @return Builder<ProductBanner>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope hero banners.
     *
     * @param  Builder<ProductBanner>  $query
     * @return Builder<ProductBanner>
     */
    public function scopeHero(Builder $query): Builder
    {
        return $query->where('position', 'hero');
    }
}
