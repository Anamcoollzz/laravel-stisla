<?php

namespace App\Models\LP;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Table('vision_missions')]
#[Fillable(['vision_eyebrow', 'vision_title', 'vision_description', 'mission_eyebrow', 'mission_title', 'mission_points'])]
class VisionMission extends Model
{
    use HasFactory;

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
