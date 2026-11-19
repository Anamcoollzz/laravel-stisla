<?php

namespace App\Models\LP;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Table('contact_inquiries')]
#[Fillable(['full_name', 'email', 'phone', 'subject', 'message', 'ip_address', 'is_read'])]
class ContactInquiry extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    /**
     * Scope unread inquiries.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope read inquiries.
     */
    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }
}
