<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CourseSemesterEnrollment;

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
    public $taken = [];
    public function definition(): array
    {
        // $course_id = \App\Models\Course::all()->pluck('id')->toArray();
        // $semester_id = \App\Models\Semester::all()->pluck('id')->toArray();
        $course_semester_id = \App\Models\CourseSemester::all()->pluck('id')->toArray();
        $student_id = \App\Models\Student::all()->pluck('id')->toArray();

        //while the unique combination is not unique, keep generating new ones
        $uniqueCompination = [
            // 'course_id' => fake()->randomElement($course_id),
            // 'semester_id' => fake()->randomElement($semester_id),
            'course_semester_id' => fake()->randomElement($course_semester_id),
            'student_id' => fake()->randomElement($student_id),
        ];
        while (true) {
            if (!in_array($uniqueCompination, $this->taken)) {
                // add the unique combination to the taken array
                $this->taken[] = $uniqueCompination;
                break;
            }
            $uniqueCompination = [
                // 'course_id' => fake()->randomElement($course_id),
                // 'semester_id' => fake()->randomElement($semester_id),
                'course_semester_id' => fake()->randomElement($course_semester_id),
                'student_id' => fake()->randomElement($student_id),
            ];
        }
        return [
            // 'course_grade' => fake()->randomFloat(2, 0, 100),
            'term_work'=> fake()->randomFloat(2,0,40),
            'exam_work'=> fake()->randomFloat(2,0,60),
            // 'course_id' => $uniqueCompination['course_id'],
            // 'semester_id' => $uniqueCompination['semester_id'],
            'course_semester_id' => $uniqueCompination['course_semester_id'],
            'student_id' => $uniqueCompination['student_id'],
        ];
    }
}
