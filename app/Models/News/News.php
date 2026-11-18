<?php

namespace App\Models\News;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
     * Scope published news.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Main thumbnail URL.
     */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if (! $this->thumbnail) {
                    return null;
                }

                if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
                    return $this->thumbnail;
                }

                return Storage::disk('public')->url($this->thumbnail);
            }
        );
    }

    /**
     * Mini thumbnail URL.
     */
    protected function thumbnailMiniUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if (! $this->thumbnail_mini) {
                    return null;
                }

                if (str_starts_with($this->thumbnail_mini, 'http://') || str_starts_with($this->thumbnail_mini, 'https://')) {
                    return $this->thumbnail_mini;
                }

                return Storage::disk('public')->url($this->thumbnail_mini);
            }
        );
    }
}
