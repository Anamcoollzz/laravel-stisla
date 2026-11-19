<?php

namespace App\Models\LP;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanyIntroduction extends Model
{
    use HasFactory;

    protected $table = 'company_introductions';

    protected $fillable = [
        'image',
        'image_alt',
        'mission_label',
        'mission_text',
        'eyebrow',
        'title',
        'paragraph_1',
        'paragraph_2',
        'feature_1_icon',
        'feature_1_title',
        'feature_1_description',
        'feature_2_icon',
        'feature_2_title',
        'feature_2_description',
        'primary_button_label',
        'primary_button_url',
        'whatsapp_button_label',
        'whatsapp_button_message',
    ];

    /**
     * Image URL accessor with smart fallback.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->image) {
                    if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                        return $this->image;
                    }

                    if (str_starts_with($this->image, 'lp-assets/') || str_starts_with($this->image, 'assets/')) {
                        return asset($this->image);
                    }

                    if (Storage::disk('public')->exists($this->image)) {
                        return Storage::disk('public')->url($this->image);
                    }
                }

                return asset('lp-assets/images/general/about-training.jpg');
            }
        );
    }
}
