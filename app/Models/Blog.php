<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'author_name',
        'image',
        'short_description',
        'content',
        'published_at',
        'order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Auto generate slug upon saving if empty.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Blog $blog) {
            if (empty($blog->slug)) {
                $baseSlug = Str::slug($blog->title);
                $slug = $baseSlug;
                $counter = 1;

                while (static::where('slug', $slug)->where('id', '!=', $blog->id ?? 0)->exists()) {
                    $slug = $baseSlug.'-'.$counter;
                    $counter++;
                }

                $blog->slug = $slug;
            }
        });
    }

    /**
     * Scope active blogs.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * All comments for this blog post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    /**
     * Approved comments for this blog post.
     */
    public function approvedComments(): HasMany
    {
        return $this->hasMany(BlogComment::class)->where('is_approved', true);
    }

    /**
     * Top-level approved comments with replies.
     */
    public function topLevelComments(): HasMany
    {
        return $this->hasMany(BlogComment::class)
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->with(['replies' => fn ($q) => $q->where('is_approved', true)->latest()])
            ->latest();
    }

    /**
     * Image URL accessor with smart fallback.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->image) {
                    if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                        return $this->image;
                    }

                    if (str_starts_with($this->image, 'mantu/') || str_starts_with($this->image, 'assets/')) {
                        return asset($this->image);
                    }

                    if (Storage::disk('public')->exists($this->image)) {
                        return Storage::disk('public')->url($this->image);
                    }
                }

                return asset('mantu/assets/img/blog/1.jpg');
            }
        );
    }
}
