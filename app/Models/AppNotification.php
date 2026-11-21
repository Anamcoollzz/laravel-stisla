<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

#[Fillable(['title', 'message', 'type', 'icon', 'action_url', 'user_id', 'is_broadcast', 'created_by'])]
class AppNotification extends Model
{
    /**
     * In-memory request-level cache keyed by user ID.
     *
     * @var array<int, array{notifications: Collection<int, self>, unread_count: int}>
     */
    protected static array $memoizedNavbarSummaries = [];

    protected static function booted(): void
    {
        static::saved(function (self $notification): void {
            static::flushNavbarCache($notification->user_id ? (int) $notification->user_id : null, (int) auth()->id());
        });

        static::deleted(function (self $notification): void {
            static::flushNavbarCache($notification->user_id ? (int) $notification->user_id : null, (int) auth()->id());
        });
    }

    /**
     * Retrieve cached navbar notifications and unread count for a user (TTL 30 seconds).
     *
     * @return array{notifications: Collection<int, self>, unread_count: int}
     */
    public static function navbarSummaryForUser(int $userId, int $ttlSeconds = 30): array
    {
        if (isset(static::$memoizedNavbarSummaries[$userId])) {
            return static::$memoizedNavbarSummaries[$userId];
        }

        return static::$memoizedNavbarSummaries[$userId] = Cache::remember(
            "stisla.nav.notifications.{$userId}",
            now()->addSeconds($ttlSeconds),
            function () use ($userId): array {
                $notifications = static::query()
                    ->forUser($userId)
                    ->with(['readers' => fn ($query) => $query->where('users.id', $userId)])
                    ->latest('id')
                    ->limit(10)
                    ->get();

                $unreadCount = static::query()
                    ->forUser($userId)
                    ->whereDoesntHave('readers', fn ($query) => $query->where('users.id', $userId))
                    ->count();

                return [
                    'notifications' => $notifications,
                    'unread_count' => $unreadCount,
                ];
            }
        );
    }

    /**
     * Flush the cached navbar notification summary for the given user ID(s).
     */
    public static function flushNavbarCache(?int ...$userIds): void
    {
        foreach ($userIds as $userId) {
            if ($userId) {
                unset(static::$memoizedNavbarSummaries[$userId]);
                Cache::forget("stisla.nav.notifications.{$userId}");
            }
        }
    }

    /**
     * Available notification types.
     *
     * @return array<string, string>
     */
    public static function types(): array
    {
        return [
            'info' => 'Info',
            'primary' => 'Primary',
            'success' => 'Success',
            'warning' => 'Warning',
            'danger' => 'Danger',
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
            'is_broadcast' => 'boolean',
        ];
    }

    /**
     * Get the target user for a direct notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the notification.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Users who have read this notification.
     */
    public function readers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'app_notification_reads', 'app_notification_id', 'user_id')
            ->withPivot('read_at');
    }

    /**
     * Scope notifications visible to a given user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where(function (Builder $q) use ($userId): void {
            $q->where('is_broadcast', true)
                ->orWhere('user_id', $userId);
        });
    }

    /**
     * Determine whether the given user has read this notification.
     */
    public function isReadBy(int $userId): bool
    {
        if ($this->relationLoaded('readers')) {
            return $this->readers->contains('id', $userId);
        }

        return DB::table('app_notification_reads')
            ->where('app_notification_id', $this->id)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Mark this notification as read for the given user.
     */
    public function markAsReadFor(int $userId): void
    {
        DB::table('app_notification_reads')->updateOrInsert(
            [
                'app_notification_id' => $this->id,
                'user_id' => $userId,
            ],
            [
                'read_at' => now(),
            ]
        );

        static::flushNavbarCache($userId);
    }
}
