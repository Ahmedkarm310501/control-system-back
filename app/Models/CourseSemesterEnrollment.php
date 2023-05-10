<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSemesterEnrollment extends Model
{
    use HasFactory;
    protected $fillable = [
        // 'course_id',
        // 'semester_id',
        'course_semester_id',
        'student_id',
        'term_work',
        'exam_work',
    ];
    protected $table = 'course_semester_enrollments';
    protected $primaryKey =null;
    public $incrementing = false;
    // public function course()
    // {
    //     return $this->belongsTo(Course::class);
    // }

    // public function semester()
    // {
    //     return $this->belongsTo(Semester::class);
    // }

    function courseSemester(){
        return $this->belongsTo(CourseSemester::class, 'course_semester_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
