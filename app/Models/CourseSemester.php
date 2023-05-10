<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSemester extends Model
{
    use HasFactory;

    function students(){
        return $this->belongsToMany(Student::class, 'course_semester_enrollments')
            ->withPivot('term_work', 'exam_work');
    }

    function users(){
        return $this->belongsToMany(User::class, 'course_user');
            // ->withPivot('term_work', 'exam_work');
    }
}
