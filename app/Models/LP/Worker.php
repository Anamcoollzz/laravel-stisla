<?php

namespace App\Models\LP;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Worker extends Model
{
    use HasFactory;

    protected $table = 'workers';

    protected $fillable = [
        'slug',
        'code',
        'name',
        'category',
        'category_label',
        'age',
        'age_bracket',
        'origin',
        'origin_region',
        'education',
        'experience_years',
        'experience_label',
        'experience_bracket',
        'status',
        'is_featured',
        'published_date',
        'photo',
        'gallery',
        'languages',
        'bio',
        'skills',
        'work_history',
        'placement_preferences',
        'training_certifications',
        'medical_check',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'experience_years' => 'float',
            'is_featured' => 'boolean',
            'published_date' => 'date',
            'gallery' => 'array',
            'languages' => 'array',
            'skills' => 'array',
            'work_history' => 'array',
            'placement_preferences' => 'array',
            'training_certifications' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $worker): void {
            if (blank($worker->slug)) {
                $worker->slug = Str::slug($worker->name);
            }
        });
    }

    /**
     * Resolve a single photo/media path to a public URL.
     */
    public static function resolveMediaUrl(?string $path): string
    {
        if ($path) {
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }

            if (str_starts_with($path, 'lp-assets/') || str_starts_with($path, 'assets/')) {
                return asset($path);
            }

            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->url($path);
            }
        }

        return asset('lp-assets/images/general/placeholder-worker.svg');
    }

    /**
     * Main photo URL accessor.
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => self::resolveMediaUrl($this->photo),
        );
    }

    /**
     * Gallery URLs accessor.
     *
     * @return Attribute<array<int, string>, never>
     */
    protected function galleryUrls(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $gallery = $this->gallery ?? [];
                if ($gallery === [] && $this->photo) {
                    $gallery = [$this->photo];
                }

                return array_map(fn (string $item): string => self::resolveMediaUrl($item), $gallery);
            },
        );
    }
}
