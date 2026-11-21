<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'name',
    'token',
    'token_prefix',
    'abilities',
    'last_used_at',
    'last_used_ip',
    'expires_at',
    'created_by',
])]
class ApiToken extends Model
{
    /**
     * Available token abilities/scopes.
     *
     * @var list<string>
     */
    public const AVAILABLE_ABILITIES = ['*', 'read', 'create', 'update', 'delete'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'abilities' => 'array',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
            'created_by' => 'integer',
        ];
    }

    /**
     * Owner of the API token.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin who issued the API token.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Issue a new API token and return both the persisted model and one-time plain text token.
     *
     * @param  list<string>  $abilities
     * @return array{token: self, plainTextToken: string}
     */
    public static function issue(
        User $user,
        string $name,
        array $abilities = ['*'],
        ?CarbonInterface $expiresAt = null,
        ?int $createdBy = null,
    ): array {
        $plainTextToken = 'stisla_'.Str::random(48);

        $token = self::query()->create([
            'user_id' => $user->id,
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'token_prefix' => substr($plainTextToken, 0, 14),
            'abilities' => $abilities !== [] ? array_values($abilities) : ['*'],
            'expires_at' => $expiresAt,
            'created_by' => $createdBy,
        ]);

        return [
            'token' => $token,
            'plainTextToken' => $plainTextToken,
        ];
    }

    /**
     * Find a valid (non-expired) token matching the provided plain text bearer token.
     */
    public static function findValidToken(string $plainTextToken): ?self
    {
        if ($plainTextToken === '') {
            return null;
        }

        $hashed = hash('sha256', $plainTextToken);

        $token = self::query()
            ->with('user')
            ->where('token', $hashed)
            ->first();

        if (! $token || $token->isExpired()) {
            return null;
        }

        return $token;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function can(string $ability): bool
    {
        $abilities = is_array($this->abilities) ? $this->abilities : [];

        return in_array('*', $abilities, true) || in_array($ability, $abilities, true);
    }
}
