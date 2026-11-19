<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id', 'province_id', 'name'])]
class Regency extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    /**
     * Get the province that owns this regency/city.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }
}
