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

use App\Services\CourseGradeService;



class CourseGradeController extends Controller
{
    use HttpResponses;

    public function getCourseGrades($course_code, $year, CourseGradeService $courseGradeService, Request $request) 
    {
        try {
            $grades = $courseGradeService->getCourseGrades($course_code, $year, $request->user());
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }

        return $this->success($grades);
    }
    
    public function addStudentToCourse(AddStudentToCourseRequest $request, CourseGradeService $courseGradeService)
    {
        $data = $request->validated();
        try {
            $courseGradeService->addStudentToCourse($data, $request->user());
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
        return $this->success('Student added to course successfully');
    }
}
