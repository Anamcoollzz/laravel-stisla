<?php

namespace App\Models;

use Database\Factories\SppPaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['student_id', 'invoice_number', 'month', 'year', 'amount', 'payment_date', 'payment_method', 'status', 'notes'])]
class SppPayment extends Model
{
    /** @use HasFactory<SppPaymentFactory> */
    use HasFactory;

    /**
     * @return array<int, string>
     */
    public static function months(): array
    {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'amount' => 'integer',
            'payment_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function monthName(): string
    {
        return __(self::months()[$this->month] ?? (string) $this->month);
    }
}
