<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FooterProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_category_id',
        'custom_name',
        'order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Related ProductCategory.
     *
     * @return BelongsTo<ProductCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    /**
     * Scope active footer product categories.
     *
     * @param  Builder<FooterProductCategory>  $query
     * @return Builder<FooterProductCategory>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Display name accessor (uses custom_name if set, otherwise category name).
     */
    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->custom_name ?: ($this->category?->name ?? '-')
        );
    }
}
