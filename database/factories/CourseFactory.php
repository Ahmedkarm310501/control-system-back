<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $department = \App\Models\Department::all()->pluck('id')->toArray();
        $courses = [
            'Introduction to Computer Science',
            'Data Structures and Algorithms',
            'Computer Networks',
            'Operating Systems',
            'Database Systems',
            'Artificial Intelligence',
        ];
        return [
            'name' => fake()->unique()->randomElement($courses),
            'course_rule_id' => \App\Models\CourseRule::factory(),
            'department_id' => fake()->randomElement($department),
        ];
    }
}
