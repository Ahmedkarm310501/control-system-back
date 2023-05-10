<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;
    // public function courses()
    // {
    //     return $this->belongsToMany(Course::class, 'course_semester_enrollments', 'semester_id', 'course_id')
    //         ->withPivot('course_grade', 'student_id');
    // }

    // public function students()
    // {
    //     return $this->belongsToMany(Student::class, 'course_semester_enrollments', 'semester_id', 'student_id')
    //         ->withPivot('course_grade', 'course_id');
    // }
    function courses(){
        return $this->belongsToMany(Course::class, 'course_semesters', 'semester_id', 'course_id');
    }
}
