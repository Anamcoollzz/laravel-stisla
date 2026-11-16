<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'cost',
        'estimated_delivery',
        'description',
        'logo',
        'order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cost' => 'float',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope active shipping methods.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Formatted cost attribute.
     */
    protected function formattedCost(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cost <= 0 ? 'Gratis Ongkir' : 'Rp '.number_format($this->cost, 0, ',', '.')
        );
    }

    /**
     * Logo URL attribute.
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->logo) {
                    return null;
                }

                if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
                    return $this->logo;
                }

                return Storage::disk('public')->url($this->logo);
            }
        );
    }
}
