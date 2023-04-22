<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSemesterEnrollment extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_id',
        'semester_id',
        'student_id',
        'course_grade',
    ];
    protected $table = 'course_semester_enrollments';
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
