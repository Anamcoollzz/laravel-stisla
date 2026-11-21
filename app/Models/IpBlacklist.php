<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Fillable([
    'ip_address',
    'reason',
    'is_active',
    'expires_at',
    'blocked_hits',
    'last_hit_at',
    'created_by',
])]
class IpBlacklist extends Model
{
    private const CACHE_KEY = 'stisla.ip_blacklists.active';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
            'blocked_hits' => 'integer',
            'last_hit_at' => 'datetime',
            'created_by' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saved(static fn () => self::flushCache());
        static::deleted(static fn () => self::flushCache());
    }

    /**
     * Admin user who created this blacklist entry.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Determine whether this entry is currently active and not expired.
     */
    public function isCurrentlyBlocked(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return $this->expires_at === null || $this->expires_at->isFuture();
    }

    /**
     * Find a currently active blacklist entry for the given IP address.
     *
     * @return array{id: int, ip_address: string, reason: ?string, expires_at: ?int}|null
     */
    public static function findActiveMatch(?string $ip): ?array
    {
        if ($ip === null || $ip === '' || ! Schema::hasTable('ip_blacklists')) {
            return null;
        }

        $entries = self::cachedActiveEntries();
        $match = $entries[$ip] ?? null;

        if (! is_array($match)) {
            return null;
        }

        if ($match['expires_at'] !== null && now()->getTimestamp() >= $match['expires_at']) {
            return null;
        }

        return $match;
    }

    /**
     * Record a blocked hit without invalidating the active blacklist cache.
     */
    public static function recordHit(int $id): void
    {
        DB::table('ip_blacklists')
            ->where('id', $id)
            ->increment('blocked_hits', 1, ['last_hit_at' => now()]);
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, array{id: int, ip_address: string, reason: ?string, expires_at: ?int}>
     */
    private static function cachedActiveEntries(): array
    {
        $cached = Cache::rememberForever(self::CACHE_KEY, static function (): array {
            return self::query()
                ->where('is_active', true)
                ->get(['id', 'ip_address', 'reason', 'expires_at'])
                ->mapWithKeys(static fn (self $entry): array => [
                    $entry->ip_address => [
                        'id' => (int) $entry->id,
                        'ip_address' => (string) $entry->ip_address,
                        'reason' => $entry->reason,
                        'expires_at' => $entry->expires_at?->getTimestamp(),
                    ],
                ])
                ->all();
        });

        if (! is_array($cached)) {
            self::flushCache();

            return self::cachedActiveEntries();
        }

        return $cached;
    }
}
