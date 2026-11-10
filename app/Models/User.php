<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'first_name', 'last_name', 'email', 'avatar', 'role', 'phone', 'address', 'city', 'state', 'postal_code', 'password', 'last_logged_in'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_logged_in' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the URL to the user's avatar.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function (?string $value, array $attributes): string {
                $avatar = $attributes['avatar'] ?? null;

                if ($avatar && Storage::disk('public')->exists($avatar)) {
                    return Storage::disk('public')->url($avatar);
                }

                return asset('assets/img/avatar/avatar-1.png');
            }
        );
    }

    /**
     * @return HasMany<Ticket, $this>
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * @return HasMany<TicketReply, $this>
     */
    public function ticketReplies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }

    /**
     * @return HasMany<ChatMessage, $this>
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    /**
     * @return HasMany<ChatMessage, $this>
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'receiver_id');
    }

    /**
     * Check if user was recently active.
     */
    public function isOnline(): bool
    {
        return $this->last_logged_in !== null && $this->last_logged_in->gt(now()->subHours(2));
    }
}
