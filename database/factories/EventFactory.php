<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+3 months');
        $endDate = fake()->optional(0.7)->dateTimeBetween($startDate, $startDate->format('Y-m-d H:i:s').' +4 hours');

        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional(0.8)->paragraphs(2, true),
            'location' => fake()->optional(0.9)->address(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'price' => fake()->optional(0.6)->randomFloat(2, 10, 500),
            'max_participants' => fake()->optional(0.7)->numberBetween(5, 100),
            'status' => fake()->randomElement(['draft', 'published', 'cancelled', 'completed']),
            'category' => fake()->optional(0.8)->randomElement([
                'Technology', 'Business', 'Health', 'Education', 'Arts', 'Sports', 'Music', 'Food'
            ]),
            'metadata' => fake()->optional(0.3)->randomElements([
                'featured' => fake()->boolean(),
                'online' => fake()->boolean(),
                'recording_available' => fake()->boolean(),
            ]),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
