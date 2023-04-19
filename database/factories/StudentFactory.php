<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departments = \App\Models\Department::all()->pluck('id')->toArray();
        return [
            'name' => fake()->name(),
            'level' => fake()->randomElement(['1', '2', '3', '4']),
            'department_id' => fake()->randomElement($departments),
        ];
    }
}
