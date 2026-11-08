<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    protected $model = Menu::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_id' => null,
            'name' => fake()->words(2, true),
            'type' => 'item',
            'icon' => 'fas fa-circle',
            'route' => null,
            'url' => '#',
            'permission' => null,
            'order' => fake()->numberBetween(1, 50),
            'is_active' => true,
        ];
    }
}
