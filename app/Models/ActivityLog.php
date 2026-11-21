<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'module',
    'action',
    'subject_type',
    'subject_id',
    'subject_label',
    'description',
    'before_values',
    'after_values',
    'ip',
    'user_agent',
])]
class ActivityLog extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'before_values' => 'array',
            'after_values' => 'array',
        ];
    }

    /**
     * User who performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record a sensitive activity log entry with before/after values.
     *
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     */
    public static function record(
        string $module,
        string $action,
        string $description,
        ?Model $subject = null,
        ?string $subjectLabel = null,
        ?array $before = null,
        ?array $after = null,
        ?string $subjectId = null,
        ?string $subjectType = null,
    ): self {
        $request = request();

        return self::query()->create([
            'user_id' => auth()->id(),
            'module' => $module,
            'action' => $action,
            'subject_type' => $subjectType ?? ($subject ? $subject::class : null),
            'subject_id' => $subjectId ?? ($subject ? (string) $subject->getKey() : null),
            'subject_label' => $subjectLabel,
            'description' => $description,
            'before_values' => $before !== null ? self::maskSensitiveValues($before) : null,
            'after_values' => $after !== null ? self::maskSensitiveValues($after) : null,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }

    /**
     * Build a normalized diff list between before and after values.
     *
     * @return list<array{field: string, before: mixed, after: mixed}>
     */
    public function diffRows(): array
    {
        $before = is_array($this->before_values) ? $this->before_values : [];
        $after = is_array($this->after_values) ? $this->after_values : [];
        $keys = array_values(array_unique(array_merge(array_keys($before), array_keys($after))));

        $rows = [];
        foreach ($keys as $key) {
            $rows[] = [
                'field' => (string) $key,
                'before' => $before[$key] ?? null,
                'after' => $after[$key] ?? null,
            ];
        }

        return $rows;
    }

    /**
     * Mask sensitive keys such as passwords, secrets, or tokens.
     *
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private static function maskSensitiveValues(array $values): array
    {
        $masked = [];

        foreach ($values as $key => $value) {
            $lowerKey = strtolower((string) $key);
            if (
                str_contains($lowerKey, 'password')
                || str_contains($lowerKey, 'secret')
                || str_contains($lowerKey, 'token')
            ) {
                $masked[$key] = filled($value) ? '********' : null;
            } else {
                $masked[$key] = $value;
            }
        }

        return $masked;
    }
}
