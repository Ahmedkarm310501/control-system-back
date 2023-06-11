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

        $courseSemester = fake()->unique()->randomElement(\App\Models\CourseSemester::all()->toArray());


        return [
            // 'course_id' => fake()->unique()->randomElement(\App\Models\Course::all()->pluck('id')->toArray()),
            // 'semester_id' => fake()->unique()->randomElement(\App\Models\Semester::all()->pluck('id')->toArray()),
            'course_id' => $courseSemester['course_id'],
            'semester_id' => $courseSemester['semester_id'],
            'course_semester_id' => $courseSemester['id'],
            'user_id' => fake()->randomElement(\App\Models\User::all()->pluck('id')->toArray()),
        ];
    }
}
