<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(5);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(100, 9999),
            'content' => $this->faker->paragraphs(2, true),
            'type' => $this->faker->randomElement(array_keys(Announcement::types())),
            'published_at' => now()->subDays($this->faker->numberBetween(0, 7)),
            'expires_at' => now()->addDays($this->faker->numberBetween(7, 30)),
            'is_active' => true,
            'is_pinned' => false,
        ];
    }
}
