<?php

namespace App\Models\News;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
                if ($this->thumbnail) {
                    if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
                        return $this->thumbnail;
                    }

                    if (str_starts_with($this->thumbnail, 'news-assets/') || file_exists(public_path($this->thumbnail))) {
                        return asset($this->thumbnail);
                    }

                    if (Storage::disk('public')->exists($this->thumbnail)) {
                        return Storage::disk('public')->url($this->thumbnail);
                    }
                }

                $index = ((max(1, (int) ($this->id ?? 1)) - 1) % 5) + 1;

                return asset("news-assets/img/blog/single_blog_{$index}.png");
            }
        );
    }

    /**
     * Mini thumbnail URL with smart fallback to news-assets.
     */
    protected function thumbnailMiniUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->thumbnail_mini) {
                    if (str_starts_with($this->thumbnail_mini, 'http://') || str_starts_with($this->thumbnail_mini, 'https://')) {
                        return $this->thumbnail_mini;
                    }

                    if (str_starts_with($this->thumbnail_mini, 'news-assets/') || file_exists(public_path($this->thumbnail_mini))) {
                        return asset($this->thumbnail_mini);
                    }

                    if (Storage::disk('public')->exists($this->thumbnail_mini)) {
                        return Storage::disk('public')->url($this->thumbnail_mini);
                    }
                }

                $index = ((max(1, (int) ($this->id ?? 1)) - 1) % 10) + 1;

                return asset("news-assets/img/post/post_{$index}.png");
            }
        );
    }
}
