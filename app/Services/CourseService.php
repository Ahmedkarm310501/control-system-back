<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Department;
use App\Models\CourseRule;
class CourseService
{

    public function addCourse($courseData){
        // get the department id
        $department = Department::where('dept_code', $courseData['dept_code'])->first();
        if(!$department){
            return false;
        }
        $course = new Course();
        $course->course_code = $courseData['course_code'];
        $course->name = $courseData['course_name'];
        $course->department_id = $department->id;
        $course->course_rule_id = CourseRule::factory()->create()->id;
        $course->save();
        return $course;
    }

    public function listCourses(){
        $courses = Course::with('department')->get();
        if(!$courses){
            return false;
        }
        return $courses;
    }

}