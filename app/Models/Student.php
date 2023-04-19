<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    function department(){
        return $this->belongsTo(Department::class);
    }
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_semester_enrollments', 'student_id', 'course_id')
            ->withPivot('course_grade', 'semester_id');
    }

    public function semesters()
    {
        return $this->belongsToMany(Semester::class, 'course_semester_enrollments', 'student_id', 'semester_id')
            ->withPivot('course_grade', 'course_id');
    }
}
