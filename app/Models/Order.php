<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'user_address_id',
        'shipping_method_id',
        'payment_method_id',
        'payment_bank_id',
        'recipient_name',
        'phone_number',
        'address_line',
        'city',
        'state',
        'postal_code',
        'shipping_name',
        'shipping_code',
        'shipping_cost',
        'shipping_estimated_delivery',
        'payment_name',
        'payment_type',
        'payment_fee',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'grand_total',
        'status',
        'payment_status',
        'payment_proof',
        'payment_proof_uploaded_at',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'shipping_cost' => 'float',
            'payment_fee' => 'float',
            'subtotal' => 'float',
            'discount_amount' => 'float',
            'grand_total' => 'float',
            'payment_proof_uploaded_at' => 'datetime',
        ];
    }

    /**
     * User who placed this order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Associated user address (if still exists).
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }

    /**
     * Shipping method.
     */
    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    /**
     * Payment method.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Payment bank.
     */
    public function paymentBank(): BelongsTo
    {
        return $this->belongsTo(PaymentBank::class);
    }

    /**
     * Order items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Formatted grand total.
     */
    protected function formattedGrandTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp '.number_format($this->grand_total, 0, ',', '.')
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

    /**
     * Formatted shipping cost.
     */
    protected function formattedShippingCost(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->shipping_cost <= 0 ? 'Gratis Ongkir' : 'Rp '.number_format($this->shipping_cost, 0, ',', '.')
        );
    }

    /**
     * Formatted payment fee.
     */
    protected function formattedPaymentFee(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->payment_fee <= 0 ? 'Bebas Biaya' : 'Rp '.number_format($this->payment_fee, 0, ',', '.')
        );
    }

    /**
     * Public URL for uploaded payment proof.
     */
    protected function paymentProofUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if (! $this->payment_proof) {
                    return null;
                }

                if (Storage::disk('public')->exists($this->payment_proof)) {
                    return Storage::disk('public')->url($this->payment_proof);
                }

                return asset($this->payment_proof);
            }
        );
    }

    /**
     * Generate unique order number.
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-'.date('Ymd').'-';
        $random = strtoupper(substr(uniqid(), -5));

        return $prefix.$random;
    }
}
