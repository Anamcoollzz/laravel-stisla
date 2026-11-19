<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'code', 'type', 'fee', 'description', 'instructions', 'logo', 'order', 'is_active'])]
class PaymentMethod extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fee' => 'float',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope active payment methods.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Formatted fee attribute.
     */
    protected function formattedFee(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->fee <= 0 ? 'Bebas Biaya' : 'Rp '.number_format($this->fee, 0, ',', '.')
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
