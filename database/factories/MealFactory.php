<?php

namespace Database\Factories;

use App\Models\Meal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'emoji' => '🍽️',
            'category' => fake()->randomElement(['Klassiek', 'Snel', 'Vega', 'Gezond', 'Comfort']),
            'time_minutes' => fake()->numberBetween(10, 60),
            'difficulty' => fake()->randomElement(['Makkelijk', 'Gemiddeld', 'Moeilijk']),
            'price' => fake()->randomFloat(2, 4, 15),
            'base_servings' => 2,
            'color_start' => '#FFE1CF',
            'color_end' => '#FFC3A2',
            'accent_color' => '#E8621E',
            'description' => fake()->sentence(),
            'ingredients' => [
                ['name' => 'ui', 'amount' => 1, 'unit' => 'st'],
                ['name' => 'knoflook', 'amount' => 2, 'unit' => 'teen'],
            ],
        ];
    }
}
