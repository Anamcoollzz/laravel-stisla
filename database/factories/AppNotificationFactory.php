<?php

namespace Database\Factories;

use App\Models\AppNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AppNotification>
 */
class AppNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'message' => $this->faker->sentence(10),
            'type' => $this->faker->randomElement(array_keys(AppNotification::types())),
            'icon' => 'fas fa-bell',
            'action_url' => null,
            'user_id' => null,
            'is_broadcast' => true,
        ];
    }
}
