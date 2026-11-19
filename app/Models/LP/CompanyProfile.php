<?php

namespace App\Models\LP;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanyProfile extends Model
{
    use HasFactory;

    protected $table = 'company_profiles';

    protected $fillable = [
        'name',
        'legal_name',
        'logo',
        'tagline',
        'footer_description',
        'copyright_year',
        'license_number',
        'whatsapp_number',
        'phone_display',
        'secondary_phone',
        'email',
        'address',
        'short_address',
        'operating_hours_weekday',
        'operating_hours_saturday',
        'operating_hours_sunday',
        'map_embed_url',
        'instagram_url',
        'facebook_url',
        'youtube_url',
        'default_whatsapp_message',
    ];

    /**
     * Logo URL accessor with smart fallback.
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->logo) {
                    if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
                        return $this->logo;
                    }

                    if (str_starts_with($this->logo, 'lp-assets/') || str_starts_with($this->logo, 'assets/')) {
                        return asset($this->logo);
                    }

                    if (Storage::disk('public')->exists($this->logo)) {
                        return Storage::disk('public')->url($this->logo);
                    }
                }

                return asset('lp-assets/images/general/logo.svg');
            }
        );
    }
}
