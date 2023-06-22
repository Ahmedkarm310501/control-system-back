<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    // ->whereHas('semesters', function($query) use ($semesterId) {
    //     $query->where('id', $semesterId);
    // })
    // ->get();
    public function run(): void
    {
        \App\Models\User::factory(4)->create();
        \App\Models\Department::factory(2)->create();
         \App\Models\Course::factory(6)->create();
         \App\Models\Student::factory(2000)->create();
         \App\Models\Semester::factory(3)->create();
            \App\Models\CourseSemester::factory(6)->create();
         \App\Models\CourseUser::factory(5)->create();
         \App\Models\CourseSemesterEnrollment::factory(2000)->create();
    }
}
