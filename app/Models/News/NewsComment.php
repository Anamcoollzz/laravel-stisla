<?php

namespace App\Models\News;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'news_id',
        'user_id',
        'parent_id',
        'name',
        'email',
        'website',
        'comment',
        'is_approved',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'news_id' => 'integer',
            'user_id' => 'integer',
            'parent_id' => 'integer',
        ];
    }

    /**
     * The news article this comment belongs to.
     *
     * @return BelongsTo<News, $this>
     */
    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class, 'news_id');
    }

    /**
     * The registered user who wrote this comment.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parent comment for replies.
     *
     * @return BelongsTo<NewsComment, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(NewsComment::class, 'parent_id');
    }

    /**
     * Child replies.
     *
     * @return HasMany<NewsComment, $this>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(NewsComment::class, 'parent_id');
    }

    /**
     * Scope approved comments.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope top-level parent comments.
     */
    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
}
