<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Semester>
 */
class SemesterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    // public $taken = [];
    public function definition(): array
    {

        return [
            // 'year' => fake()->year(), year between 2019 and 2022
            'year' => fake()->unique()->numberBetween(2019, 2023),
            'term' => fake()->unique()->randomElement(['first', 'second', 'third']),
        ];
    }
}
