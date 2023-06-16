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

    public function getCourse($course){
        $course = Course::find($course);
        $rule = $course->rule;
        $department = Department::find($course->department_id)->select('dept_code')->first();
        // dd($department);
        // $course = Course::with('department')->where('course_code', $course)->first();
        if(!$course){
            return false;
        }
        $course['deptName']  = $department->name;
        $course['rule']  = $rule;
        $res ;
        $res['courseID'] = $course->course_code;
        $res['courseName'] = $course->name;
        $res['termWork'] = $course->rule->term_work;
        $res['examWork'] = $course->rule->exam_work;
        $res['department'] = $department->dept_code;
        $res['instructor'] = $course->rule->instructor;
        $res['totalGrade'] = $course->rule->total;
        // $res['instructor'] = $course->rule->instructor;
        return $res;
    }
}

// courseID = 'IS123';
//   courseNamee = 'Intro to Database Systems';
//   termWorkk = 40;
//   examWorkk = 60;
//   departmentt = 'IS';
//   instructorr = 'Ali Zidane';
//   totalGradee = this.termWorkk + this.examWorkk