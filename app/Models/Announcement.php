<?php

namespace App\Models;

use Database\Factories\AnnouncementFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Announcement extends Model
{
    /** @use HasFactory<AnnouncementFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'type',
        'published_at',
        'expires_at',
        'is_active',
        'is_pinned',
        'created_by',
    ];

    /**
     * Available announcement types.
     *
     * @return array<string, string>
     */
    public static function types(): array
    {
        return [
            'info' => 'Info',
            'warning' => 'Warning',
            'urgent' => 'Urgent',
            'event' => 'Event',
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
            'is_pinned' => 'boolean',
        ];
    }

    /**
     * Boot model event hooks.
     */
    protected static function booted(): void
    {
        static::creating(function (Announcement $announcement): void {
            if (blank($announcement->slug)) {
                $announcement->slug = static::generateUniqueSlug($announcement->title);
            }
        });
    }

    /**
     * Generate a unique slug for an announcement title.
     */
    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'announcement';
        $slug = $base;
        $counter = 1;

        while (static::query()
            ->when($ignoreId !== null, fn (Builder $q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $counter++;
            $slug = "{$base}-{$counter}";
        }

        return $slug;
    }

    /**
     * Get the user who created the announcement.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope query to active announcements.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
