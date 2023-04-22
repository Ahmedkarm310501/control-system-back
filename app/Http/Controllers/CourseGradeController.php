<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\CourseUser;
use App\Models\CourseSemesterEnrollment;
use App\Models\Course;
use App\Models\Semester;
use App\Models\Student;
use App\Http\Requests\AddStudentToCourseRequest;




class CourseGradeController extends Controller
{
    use HttpResponses;

    public function getCourseGrades($course_code,$year, Request $request)
    {
        $course = Course::where('course_code', $course_code)->first();
        if(!$course){
            return $this->error('Course not found', 404);
        }
        // check if the user has access to the course
        $course_user = CourseUser::where('user_id', $request->user()->id)
        ->where('course_id', $course->id)->first();
        if(!$course_user){
            return $this->error('You do not have access to this course', 403);
        }
        // get semster id
        $semester = Semester::where('year', $year)->first();
        if(!$semester){
            return $this->error('Semester not found', 404);
        }
        // get course semester enrollment with the semester id and course id 
        $course_semester_enrollment = CourseSemesterEnrollment::with('student:name,id')
        ->where('course_id', $course->id)
        ->where('semester_id', $semester->id)
        ->get()
        ->map(function ($enrollment) {
            $enrollment->total_grade = $enrollment->term_work + $enrollment->exam_work;
            return $enrollment;
        });
        if(!$course_semester_enrollment){
            return $this->error('Course semester enrollment not found', 404);
        }

        return $this->success($course_semester_enrollment);
        
        
    }


    public function addStudentToCourse(addStudentToCourseRequest $request)
    {
        $data = $request->validated();
        $course = Course::find($data['course_id']);
        if(!$course){
            return $this->error('Course not found', 404);
        }
        // check if the user has access to the course
        $course_user = CourseUser::where('user_id', $request->user()->id)
        ->where('course_id', $course->id)->first();
        if(!$course_user){
            return $this->error('You do not have access to this course', 403);
        }
        // get semster id
        $semester = Semester::find($data['semester_id']);
        if(!$semester){
            return $this->error('Semester not found', 404);
        }
        $student = Student::find($data['student_id']);
        if(!$student){
            $student= Student::create([
                'name' => $data['student_name'],
                'id' => $data['student_id'],
            ]);
        }
        $course_semester_enrollment = CourseSemesterEnrollment::create([
            'course_id' => $course->id,
            'semester_id' => $data['semester_id'],
            'student_id' => $student->id,
        ]);
        if($course_semester_enrollment){
            return $this->successMessage('Student added to course successfully');
        }else{
            return $this->error('Student not added to course', 422);
        }
    }
}
