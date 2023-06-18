<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseSemester;
use App\Models\Semester;
use App\Models\Department;
use App\Models\CourseRule;
use App\Models\CourseUser;
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
        $department = Department::find($course->department_id)->select('dept_code','name')->first();
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
        $res['deptName'] = $department->name;
        $res['instructor'] = $course->rule->instructor;
        $res['totalGrade'] = $course->rule->total;
        return $res;
    }

    public function editCourse($courseData){
        if($courseData['term_work'] + $courseData['exam_work'] != $courseData['total']){
            throw new \Exception('Term work + exam work must = total', 403);
        }
        $course = Course::find($courseData['course_id']);
        if(!$course){
            return false;
        }
        $course_user = CourseUser::where('course_id', $courseData['course_id'])
        ->where('semester_id', $courseData['semester_id'])->where('user_id', auth()->user()->id)->first();
        if(!$course_user){
            return false;
        }
        $department = Department::where('dept_code', $courseData['dept_code'])->first();
        if(!$department){
            return false;
        }
        $courseRule = $course->rule;

        $course->course_code = $courseData['course_code'];
        $course->name = $courseData['course_name'];
        $course->department_id = $department->id;
        $courseRule->term_work = $courseData['term_work'];
        $courseRule->exam_work = $courseData['exam_work'];
        $courseRule->instructor = $courseData['instructor'];
        $courseRule->total = $courseData['total'];
        $course->save();
        $courseRule->save();
        return $course;
    }
    public function getCoursesInSemesterMerge(){
        $Allcourses = Course::all();
        $departments = Department::all();
        // get the leatest semester
        $semester = Semester::latest()->first();
        // get the courses id from course semester table by semester id
        $courses_id = CourseSemester::where('semester_id',$semester->id)->get('course_id');
        $coursesInSemester = [];
        foreach ($courses_id as $course_id){
            $coursesInSemester[] = Course::find($course_id->course_id);
        }
        return [
            'courses' => $Allcourses,
            'departments' => $departments,
            'coursesInSemester' => $coursesInSemester,
            'newestSemester' => $semester
        ];

    }
}

