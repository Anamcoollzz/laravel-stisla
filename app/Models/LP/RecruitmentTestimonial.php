<?php

namespace App\Models\LP;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RecruitmentTestimonial extends Model
{
    use HasFactory;

    protected $table = 'recruitment_testimonials';

    protected $fillable = [
        'name',
        'location',
        'service',
        'quote',
        'avatar',
        'rating',
        'order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope active testimonials.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Avatar URL accessor with smart fallback.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->avatar) {
                    if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                        return $this->avatar;
                    }

                    if (str_starts_with($this->avatar, 'lp-assets/') || str_starts_with($this->avatar, 'assets/')) {
                        return asset($this->avatar);
                    }

                    if (Storage::disk('public')->exists($this->avatar)) {
                        return Storage::disk('public')->url($this->avatar);
                    }
                }

                return asset('lp-assets/images/workers/worker-1.jpg');
            }
        );
    }
}
