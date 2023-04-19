<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
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
}
