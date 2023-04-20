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
            'CS101' => 'Introduction to Computer Science',
            'CS221' => 'Data Structures and Algorithms',
            'IT111' => 'Computer Networks',
            'CS104' => 'Operating Systems',
            'IS221' => 'Database Systems',
            'CS106' => 'Artificial Intelligence',
        ];
        $course_code = fake()->unique()->randomElement(array_keys($courses));
        
        return [
            'course_code' => $course_code,
            'name' => $courses[$course_code],
            'course_rule_id' => \App\Models\CourseRule::factory(),
            'department_id' => fake()->randomElement($department),
        ];
    }
}
