<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'course_code',
        'name',
        'department_id',
        'course_rule_id',
    ];
    use HasFactory;
    function users(){
        return $this->belongsToMany(User::class);
    }
    function rule(){
        return $this->belongsTo(CourseRule::class, 'course_rule_id');
    }
    function department(){
        return $this->belongsTo(Department::class);
    }
    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_semester_enrollments', 'course_id', 'student_id')
            ->withPivot('course_grade', 'semester_id');
    }

    public function semesters()
    {
        return $this->belongsToMany(Semester::class, 'course_semester_enrollments', 'course_id', 'semester_id')
            ->withPivot('course_grade', 'student_id');
    }
}
