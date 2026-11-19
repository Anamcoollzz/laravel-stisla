<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'product_id', 'product_title', 'product_slug', 'product_thumbnail', 'price', 'quantity', 'subtotal'])]
class OrderItem extends Model
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'float',
            'quantity' => 'integer',
            'subtotal' => 'float',
        ];
    }

    /**
     * Parent order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Associated product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Formatted price.
     */
    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp '.number_format($this->price, 0, ',', '.')
        );
    }

    /**
     * Formatted subtotal.
     */
    protected function formattedSubtotal(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp '.number_format($this->subtotal, 0, ',', '.')
        );
    }
}
