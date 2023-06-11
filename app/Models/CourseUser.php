<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseUser extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','course_id','semester_id'];
    protected $table = 'course_user';

    public function course(){
        return $this->belongsTo(Course::class);
    }
}
