<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['title', 'description', 'icon_svg', 'icon_image', 'order', 'is_active'])]
class StoreService extends Model
{
    use HasFactory;

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
     * Scope active store services.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Icon image URL accessor.
     */
    protected function iconImageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->icon_image) {
                    return null;
                }

                if (str_starts_with($this->icon_image, 'http://') || str_starts_with($this->icon_image, 'https://')) {
                    return $this->icon_image;
                }

                if (Storage::disk('public')->exists($this->icon_image)) {
                    return Storage::disk('public')->url($this->icon_image);
                }

                return asset($this->icon_image);
            }
        );
    }
}
