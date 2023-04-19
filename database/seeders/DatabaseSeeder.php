<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(1)->create();
        \App\Models\Department::factory(2)->create();
         \App\Models\Course::factory(5)->create();
         \App\Models\Student::factory(20)->create();
         \App\Models\Semester::factory(1)->create();
         \App\Models\CourseUser::factory(5)->create();
         \App\Models\CourseSemesterEnrollment::factory(50)->create();
    }
}
