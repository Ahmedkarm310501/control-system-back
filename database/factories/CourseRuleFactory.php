<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseRule>
 */
class CourseRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'term_work' => 40,
            'exam_work' => 60,
            'exam_pass_mark' => 18,
            'total' => 100,
            'instructor' => fake()->name(),
        ];
    }
}