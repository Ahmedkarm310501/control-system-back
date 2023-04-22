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
use App\Http\Requests\addStudentsToCourseRequest;
use App\Http\Requests\DeleteStudentFromCourseRequest;
use App\Http\Requests\AddStudGradeRequest;


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

    public function addStudentsToCourseExcel(AddStudentsToCourseRequest $request, CourseGradeService $courseGradeService)
    {
        $data = $request->validated();
        try {
           $data=  $courseGradeService->addStudentsToCourseExcel($data, $request->user());
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
        return $this->success($data['numOfMissingFields'],201,'Students added to course successfully');
    }

    public function deleteStudentFromCourse(DeleteStudentFromCourseRequest $request, CourseGradeService $courseGradeService)
    {
        try {
            $courseGradeService->deleteStudentFromCourse($request->validated());
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
        return $this->success('Student deleted from course successfully');
    }

    public function addOneStudentGrade(AddStudGradeRequest $request, CourseGradeService $courseGradeService)
    {
        $data = $request->validated();
        try {
            $courseGradeService->addOneStudentGrade($data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
        return $this->success('Student grade added successfully');
    }
}
