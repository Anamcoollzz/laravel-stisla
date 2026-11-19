<?php

namespace App\Models;

use Database\Factories\AppNotificationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class AppNotification extends Model
{
    /** @use HasFactory<AppNotificationFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'message',
        'type',
        'icon',
        'action_url',
        'user_id',
        'is_broadcast',
        'created_by',
    ];

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
    }
}
