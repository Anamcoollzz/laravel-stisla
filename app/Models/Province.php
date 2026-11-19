<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id', 'name'])]
class Province extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    /**
     * Get the regencies/cities belonging to this province.
     */
    public function regencies(): HasMany
    {
        return $this->hasMany(Regency::class, 'province_id', 'id');
    }
}
