<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseUser>
 */
class CourseUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'course_id' => fake()->unique()->randomElement(\App\Models\Course::all()->pluck('id')->toArray()),
            'course_semester_id' => fake()->unique()->randomElement(\App\Models\CourseSemester::all()->pluck('id')->toArray()),
            'user_id' => fake()->randomElement(\App\Models\User::all()->pluck('id')->toArray()),
        ];
    }
}
