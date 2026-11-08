<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductColor extends Model
{
    use HasFactory;

    protected $table = 'product_colors';

    protected $fillable = [
        'name',
        'color_code',
    ];

    /**
     * @return HasMany<Product>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'color_id');
    }
}

