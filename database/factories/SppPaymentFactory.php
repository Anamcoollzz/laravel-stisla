<?php

namespace Database\Factories;

use App\Models\SppPayment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SppPayment>
 */
class SppPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'invoice_number' => strtoupper(fake()->unique()->bothify('SPP-2026-#####')),
            'month' => fake()->numberBetween(1, 12),
            'year' => 2026,
            'amount' => fake()->randomElement([250000, 300000, 500000]),
            'payment_date' => fake()->date('Y-m-d'),
            'payment_method' => fake()->randomElement(['cash', 'transfer']),
            'status' => fake()->randomElement(['paid', 'unpaid']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
