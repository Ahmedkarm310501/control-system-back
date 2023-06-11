<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseSemester>
 */
class CourseSemesterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public $taken = [];
    public function definition(): array
    {
        $course_id = \App\Models\Course::all()->pluck('id')->toArray();
        $semester_id = \App\Models\Semester::all()->pluck('id')->toArray();

        $uniqueCompination = [
            'course_id' => fake()->randomElement($course_id),
            'semester_id' => fake()->randomElement($semester_id),
        ];
        while (true) {
            if (!in_array($uniqueCompination, $this->taken)) {
                $this->taken[] = $uniqueCompination;
                break;
            }
            $uniqueCompination = [
                'course_id' => fake()->randomElement($course_id),
                'semester_id' => fake()->randomElement($semester_id),
            ];
        }
        

        return [
            'course_id' => $uniqueCompination['course_id'],
            'semester_id' => $uniqueCompination['semester_id'],
        ];
    }
}
