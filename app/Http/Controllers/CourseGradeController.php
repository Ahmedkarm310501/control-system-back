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
use App\Http\Requests\NumberStudentsRequest;
use App\Services\CourseGradeService;
use App\Http\Requests\addStudentsToCourseRequest;
use App\Http\Requests\DeleteStudentFromCourseRequest;
use App\Http\Requests\AddStudGradeRequest;
use App\Http\Requests\DeleteCourseGradesRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GradesExport;
use App\Http\Requests\ExportCourseGradesRequest;


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

    public function deleteCourseGrades(DeleteCourseGradesRequest $request, CourseGradeService $courseGradeService)
    {
        $data = $request->validated();
        try {
            $courseGradeService->deleteCourseGrades($data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
        return $this->success('Course grades deleted successfully');
    }

    public function addStudentsGradesExcel(AddStudentsToCourseRequest $request, CourseGradeService $courseGradeService)
    {
        $data = $request->validated();
        try {
            $data = $courseGradeService->addStudentsGradesExcel($data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
        if (count($data['wrongFormat']) == 0 && $data['studWithNoGrade'] ==false )
            return $this->success($data['course_semester_enrollment'],201,'grades added successfully');
        else if(count($data['wrongFormat']) == 0 && $data['studWithNoGrade'] ==true)
            return $this->success($data['course_semester_enrollment'],201,'grades added successfully but there is some students with no grade');
        else
            return $this->success($data['course_semester_enrollment'],201,'grades added successfully but there is missing data at row: '.implode(', ', $data['wrongFormat']).' and there is some students with no grade');

    }
    public function exportCourseGrades(CourseGradeService $courseGradeService,ExportCourseGradesRequest $request)
    {
        $data = $request->validated();
        try {
            $grades = $courseGradeService->exportCourseGrades($data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
        // dd($grades);
        return Excel::download(new GradesExport($data), 'course.xlsx');
        // return $this->success($grades,200,'Course grades exported successfully');
    }
}
