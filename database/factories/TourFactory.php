<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'travel_id' => rand(1, 10),
            'name' => fake()->text(20),
            'starting_date' => fake()->date('Y-m-d'),
            'ending_date'=> fake()->date('Y-m-d'),
            'price'=> rand(10000, 100000),
        ];
    }
}
