<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['media_file_id', 'version_number', 'disk', 'path', 'mime_type', 'size', 'width', 'height', 'note', 'created_by'])]
class MediaFileVersion extends Model
{
    use HasFactory;

    /**
     * Get the parent media file.
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'media_file_id');
    }

    /**
     * Get the user who created this version.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the URL for this version file.
     */
    public function url(): string
    {
        return Storage::disk($this->disk ?: 'public')->url($this->path);
    }
}
