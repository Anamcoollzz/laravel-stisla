<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['folder_id', 'name', 'title', 'alt_text', 'description', 'tags', 'disk', 'path', 'mime_type', 'extension', 'size', 'width', 'height', 'visibility', 'allowed_roles', 'is_shared', 'share_token', 'share_expires_at', 'current_version', 'uploaded_by'])]
class MediaFile extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'allowed_roles' => 'array',
            'is_shared' => 'boolean',
            'share_expires_at' => 'datetime',
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'current_version' => 'integer',
        ];
    }

    /**
     * Get the folder containing this file.
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    /**
     * Get the uploader user.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get all stored versions of this file.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(MediaFileVersion::class)->orderByDesc('version_number');
    }

    /**
     * Determine whether this file is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    /**
     * Get the public/preview URL for the file.
     */
    public function url(): string
    {
        return Storage::disk($this->disk ?: 'public')->url($this->path);
    }

    /**
     * Format byte size into human-readable string.
     */
    public function formattedSize(): string
    {
        $bytes = (int) $this->size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    /**
     * Determine whether the given user is allowed to access this file.
     */
    public function canBeAccessedBy(?User $user): bool
    {
        if ($this->visibility === 'public') {
            return true;
        }

        if (! $user) {
            return false;
        }

        if ($user->hasRole('admin') || (int) $this->uploaded_by === (int) $user->id) {
            return true;
        }

        if ($this->visibility === 'role') {
            $roles = (array) ($this->allowed_roles ?? []);

            return $roles !== [] && $user->hasAnyRole($roles);
        }

        return false;
    }

    /**
     * Scope files accessible by the given user.
     */
    public function scopeAccessibleBy(Builder $query, ?User $user): Builder
    {
        if ($user && $user->hasRole('admin')) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($user): void {
            $q->where('visibility', 'public');

            if ($user) {
                $q->orWhere('uploaded_by', $user->id);

                $userRoles = $user->getRoleNames()->all();
                if ($userRoles !== []) {
                    $q->orWhere(function (Builder $rq) use ($userRoles): void {
                        $rq->where('visibility', 'role')
                            ->where(function (Builder $jsonQ) use ($userRoles): void {
                                foreach ($userRoles as $roleName) {
                                    $jsonQ->orWhereJsonContains('allowed_roles', $roleName);
                                }
                            });
                    });
                }
            }
        });
    }
}
