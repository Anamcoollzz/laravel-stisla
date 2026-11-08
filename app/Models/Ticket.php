<?php

namespace App\Models;

use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'title', 'description', 'status', 'priority'])]
class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<TicketReply, $this>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }

    /**
     * Priority badge class.
     */
    public function priorityBadgeClass(): string
    {
        return match ($this->priority) {
            'high' => 'badge-danger',
            'medium' => 'badge-warning',
            'low' => 'badge-info',
            default => 'badge-secondary',
        };
    }

    /**
     * Status badge class.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'open' => 'badge-success',
            'in_progress' => 'badge-info',
            'closed' => 'badge-secondary',
            default => 'badge-light',
        };
    }
}
