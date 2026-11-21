<?php

namespace App\Models;

use App\Services\UserSessionService;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

#[Fillable([
    'user_id',
    'email',
    'event',
    'status',
    'ip_address',
    'user_agent',
    'browser',
    'platform',
    'is_mobile',
    'notes',
    'logged_at',
])]
class LoginHistory extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'is_mobile' => 'boolean',
            'logged_at' => 'datetime',
        ];
    }

    /**
     * User associated with this login event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record a login/logout authentication event safely.
     */
    public static function record(
        string $event,
        string $status,
        ?User $user = null,
        ?string $email = null,
        ?string $notes = null,
    ): ?self {
        if (! Schema::hasTable('login_histories')) {
            return null;
        }

        $request = request();
        $userAgent = (string) ($request?->userAgent() ?? '');
        $sessionService = app(UserSessionService::class);

        return self::query()->create([
            'user_id' => $user?->id,
            'email' => $email ?? $user?->email,
            'event' => $event,
            'status' => $status,
            'ip_address' => $request?->ip(),
            'user_agent' => $userAgent !== '' ? $userAgent : null,
            'browser' => $sessionService->detectBrowser($userAgent),
            'platform' => $sessionService->detectPlatform($userAgent),
            'is_mobile' => $sessionService->isMobile($userAgent),
            'notes' => $notes,
            'logged_at' => now(),
        ]);
    }
}
