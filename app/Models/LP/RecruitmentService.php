<?php

namespace App\Models\LP;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecruitmentService extends Model
{
    use HasFactory;

    protected $table = 'recruitment_services';

    protected $fillable = [
        'slug',
        'category_key',
        'title',
        'subtitle',
        'icon',
        'image',
        'short_description',
        'description',
        'responsibilities',
        'skills',
        'benefits',
        'order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'responsibilities' => 'array',
            'skills' => 'array',
            'benefits' => 'array',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $service): void {
            if (blank($service->slug)) {
                $service->slug = Str::slug($service->title);
            }
        });
    }

    /**
     * Scope active services.
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
