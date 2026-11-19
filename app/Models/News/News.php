<?php

namespace App\Models\News;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'thumbnail_mini',
        'thumbnail',
        'published_at',
        'status',
        'views_count',
        'news_category_id',
        'publisher_user_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'views_count' => 'integer',
            'news_category_id' => 'integer',
            'publisher_user_id' => 'integer',
        ];
    }

    /**
     * Category of this news item.
     *
     * @return BelongsTo<NewsCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    /**
     * Publisher user of this news item.
     *
     * @return BelongsTo<User, $this>
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'publisher_user_id');
    }

    /**
     * Tags attached to this news item.
     *
     * @return BelongsToMany<NewsTag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(NewsTag::class, 'news_news_tag', 'news_id', 'news_tag_id');
    }

    /**
     * All comments on this news item.
     *
     * @return HasMany<NewsComment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(NewsComment::class, 'news_id');
    }

    /**
     * Approved top-level comments with approved replies.
     *
     * @return HasMany<NewsComment, $this>
     */
    public function approvedTopLevelComments(): HasMany
    {
        return $this->hasMany(NewsComment::class, 'news_id')
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->with(['user', 'replies' => fn ($q) => $q->where('is_approved', true)->with('user')->latest()])
            ->latest();
    }

    /**
     * Scope published news.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Main thumbnail URL with smart fallback to news-assets.
     */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($resolved = $this->resolveMediaPath($this->thumbnail)) {
                    return $resolved;
                }

                if ($resolvedMini = $this->resolveMediaPath($this->thumbnail_mini)) {
                    return $resolvedMini;
                }

                $index = ((max(1, (int) ($this->id ?? 1)) - 1) % 5) + 1;

                return asset("news-assets/img/blog/single_blog_{$index}.png");
            }
        );
    }

    /**
     * Mini thumbnail URL synchronized with thumbnailUrl when not explicitly set.
     */
    protected function thumbnailMiniUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($resolvedMini = $this->resolveMediaPath($this->thumbnail_mini)) {
                    return $resolvedMini;
                }

                return $this->thumbnail_url;
            }
        );
    }

    /**
     * Resolve a stored media path or URL to a public URL.
     */
    protected function resolveMediaPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'news-assets/') || file_exists(public_path($path))) {
            return asset($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return null;
    }
}
