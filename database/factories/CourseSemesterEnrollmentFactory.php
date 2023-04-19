<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseSemesterEnrollment>
 */
class CourseSemesterEnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $course_id = \App\Models\Course::all()->pluck('id')->toArray();
        $semester_id = \App\Models\Semester::all()->pluck('id')->toArray();
        $student_id = \App\Models\Student::all()->pluck('id')->toArray();
        return [
            'course_grade' => fake()->randomFloat(2, 0, 100),
            'course_id' => fake()->randomElement($course_id),
            'semester_id' =>    fake()->randomElement($semester_id),
            'student_id' => fake()->randomElement($student_id),
        ];
    }
}
