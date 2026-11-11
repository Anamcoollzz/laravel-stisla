<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'parent_id',
        'name',
        'email',
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
        ];
    }

    /**
     * The blog post this comment belongs to.
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Parent comment for replies.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(BlogComment::class, 'parent_id');
    }

    /**
     * Child replies.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'parent_id');
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

    /**
     * User avatar URL accessor.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $hash = md5(strtolower(trim($this->email ?? 'user@example.com')));

                return "https://www.gravatar.com/avatar/{$hash}?s=120&d=mp";
            }
        );
    }
}
