<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    function courses(){
        return $this->hasMany(Course::class,'department_id');
    }
    function students(){
        return $this->hasMany(Student::class,'department_id');
    }
}
