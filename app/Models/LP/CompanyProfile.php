<?php

namespace App\Models\LP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    use HasFactory;

    protected $table = 'company_profiles';

    protected $fillable = [
        'name',
        'legal_name',
        'tagline',
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
}
