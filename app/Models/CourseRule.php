<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseRule extends Model
{
    use HasFactory;
    function course(){
        return $this->hasOne(Course::class);
    }
}
