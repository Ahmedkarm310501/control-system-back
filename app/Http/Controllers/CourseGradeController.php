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



class CourseGradeController extends Controller
{
    use HttpResponses;

    public function getCourseGrades($course_code, $year, CourseGradeService $courseGradeService, Request $request)
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
}
// if (count($data['wrongFormat']) == 0)
//             return $this->success($data['course_semester_enrollment'],201,'grades added successfully');
//         else
//             return $this->success($data['course_semester_enrollment'],201,'grades added successfully but there is missing data at row: '.implode(', ', $data['wrongFormat']).'');
