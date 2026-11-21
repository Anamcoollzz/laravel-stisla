<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'task_key',
    'command',
    'description',
    'expression',
    'status',
    'exit_code',
    'duration_ms',
    'output',
    'triggered_by',
    'user_id',
    'started_at',
    'finished_at',
])]
class ScheduledTaskRun extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'exit_code' => 'integer',
            'duration_ms' => 'integer',
            'user_id' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    /**
     * User who manually triggered this task run (null if triggered by scheduler).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
