<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PaymentBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name',
        'bank_code',
        'account_name',
        'account_number',
        'branch',
        'logo',
        'qr_code_image',
        'order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope active banks.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Logo URL attribute.
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->logo) {
                    return null;
                }

                if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
                    return $this->logo;
                }

                return Storage::disk('public')->url($this->logo);
            }
        );
    }

    /**
     * QR code image URL attribute.
     */
    protected function qrCodeUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->qr_code_image) {
                    return null;
                }

                if (str_starts_with($this->qr_code_image, 'http://') || str_starts_with($this->qr_code_image, 'https://')) {
                    return $this->qr_code_image;
                }

                return Storage::disk('public')->url($this->qr_code_image);
            }
        );
    }
}
