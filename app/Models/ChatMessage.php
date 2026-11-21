<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

#[Fillable(['sender_id', 'receiver_id', 'message', 'is_read', 'read_at'])]
class ChatMessage extends Model
{
    use HasFactory;

    /**
     * In-memory request-level cache keyed by user ID.
     *
     * @var array<int, array{messages: Collection<int, self>, unread_count: int}>
     */
    protected static array $memoizedNavbarSummaries = [];

    protected static function booted(): void
    {
        static::saved(function (self $message): void {
            static::flushNavbarCache((int) $message->receiver_id, (int) $message->sender_id);
        });

        static::deleted(function (self $message): void {
            static::flushNavbarCache((int) $message->receiver_id, (int) $message->sender_id);
        });
    }

    /**
     * Retrieve cached navbar chat messages and unread count for a user (TTL 30 seconds).
     *
     * @return array{messages: Collection<int, self>, unread_count: int}
     */
    public static function navbarSummaryForUser(int $userId, int $ttlSeconds = 30): array
    {
        if (isset(static::$memoizedNavbarSummaries[$userId])) {
            return static::$memoizedNavbarSummaries[$userId];
        }

        return static::$memoizedNavbarSummaries[$userId] = Cache::remember(
            "stisla.nav.chat.{$userId}",
            now()->addSeconds($ttlSeconds),
            function () use ($userId): array {
                $messages = static::query()
                    ->where('receiver_id', $userId)
                    ->with('sender')
                    ->latest('id')
                    ->limit(30)
                    ->get()
                    ->unique('sender_id')
                    ->take(5)
                    ->values();

                $unreadCount = static::query()
                    ->where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->count();

                return [
                    'messages' => $messages,
                    'unread_count' => $unreadCount,
                ];
            }
        );
    }

    /**
     * Flush the cached navbar chat summary for the given user ID(s).
     */
    public static function flushNavbarCache(?int ...$userIds): void
    {
        foreach ($userIds as $userId) {
            if ($userId) {
                unset(static::$memoizedNavbarSummaries[$userId]);
                Cache::forget("stisla.nav.chat.{$userId}");
            }
        }
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Scope messages between two specific users.
     *
     * @param  Builder<ChatMessage>  $query
     * @return Builder<ChatMessage>
     */
    public function scopeBetweenUsers(Builder $query, int $user1Id, int $user2Id): Builder
    {
        return $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user1Id)->where('receiver_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user2Id)->where('receiver_id', $user1Id);
        });
    }
}
