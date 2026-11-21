<?php

namespace App\Models\LP;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Table('lp_galleries')]
#[Fillable(['title', 'category', 'date', 'description', 'image', 'order', 'is_active'])]
class LpGallery extends Model
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
     * Scope active gallery items.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Image URL accessor with smart fallback.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->image) {
                    if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                        return $this->image;
                    }

                    if (str_starts_with($this->image, 'lp-assets/') || str_starts_with($this->image, 'assets/')) {
                        return asset($this->image);
                    }

                    if (Storage::disk('public')->exists($this->image)) {
                        return Storage::disk('public')->url($this->image);
                    }
                }

                return asset('lp-assets/images/general/placeholder-gallery.svg');
            }
        );
    }
}
