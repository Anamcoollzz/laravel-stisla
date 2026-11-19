<?php

namespace App\Models\LP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisionMission extends Model
{
    use HasFactory;

    protected $table = 'vision_missions';

    protected $fillable = [
        'vision_eyebrow',
        'vision_title',
        'vision_description',
        'mission_eyebrow',
        'mission_title',
        'mission_points',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mission_points' => 'array',
        ];
    }
}
