<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image',
        'order',
        'is_primary',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_primary' => 'boolean',
        ];
    }

    /**
     * Product relationship.
     *
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Image URL accessor with smart fallback.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $fallback = asset('assets/img/products/product-1-50.png');
                if (file_exists(public_path('mantu/assets/img/product/1.jpg'))) {
                    $fallback = asset('mantu/assets/img/product/1.jpg');
                }

                if (empty($this->image)) {
                    return $fallback;
                }

                if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                    return $this->image;
                }

                if (file_exists(public_path($this->image))) {
                    return asset($this->image);
                }

                if (file_exists(storage_path('app/public/'.$this->image))) {
                    return asset('storage/'.$this->image);
                }

                $fixed = str_replace('mantu/assets/img/products/', 'mantu/assets/img/product/', $this->image);
                if (file_exists(public_path($fixed))) {
                    return asset($fixed);
                }

                return $fallback;
            }
        );
    }
}
