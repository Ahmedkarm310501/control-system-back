<?php

namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Http\Requests\AddCourseRequest;
use App\Http\Requests\ImportCoursesRequest;
use App\Http\Requests\EditCourseSettingsRequest;
use App\Http\Requests\DeleteCourseRequest;
use App\Http\Requests\StudentCoursesRequest;
use App\Services\CourseService;

class CourseController extends Controller
{
    use HttpResponses;

    public function addCourse(AddCourseRequest $request, CourseService $courseService)
    {
        $course = $courseService->addCourse($request->validated());
        if(!$course){
            return $this->error('Course not added', 500);
        }
        return $this->successMessage('Course added successfully' , 201);
    }

    public function listCourses(CourseService $courseService)
    {
        $courses = $courseService->listCourses();
        if(!$courses){
            return $this->error('Courses not found', 404);
        }
        return $this->success($courses, 200 , 'all courses');
    }

    public function listCoursesInSemester(CourseService $courseService)
    {
        $courses = $courseService->listCoursesInSemester();
        if(!$courses){
            return $this->error('Courses not found', 404);
        }
        return $this->success($courses, 200 , 'all courses');
    }



    public function getCourse($course, CourseService $courseService)
    {
        $course = $courseService->getCourse($course);
        if(!$course){
            return $this->error('Course not found', 404);
        }
        return $this->success($course, 200 , 'course');
    }

    public function editCourse(EditCourseSettingsRequest $request, CourseService $courseService)
    {
        // $course = $courseService->editCourse($request->validated());
        // if(!$course){
        //     return $this->error('Course not found', 404);
        // }
        // return $this->successMessage('Course edited successfully' , 200);
        try{
            $course = $courseService->editCourse($request->validated());
            // return $this->success($course, 200 , 'course');
            return $this->successMessage('Course edited successfully' , 200);
        }catch(\Exception $e){
            return $this->error($e->getMessage(), 500);
        }
    }
    public function getCoursesInSemesterMerge(CourseService $courseService)
    {
        $courses = $courseService->getCoursesInSemesterMerge();
        if(!$courses){
            return $this->error('Courses not found', 404);
        }
        return $this->success($courses, 200 , 'all courses');
    }
    public function importCourses(ImportCoursesRequest $request, CourseService $courseService)
    {
        $file = $request->file('courses');
        $courseData = $courseService->importCourses($file);
        if (!$courseData) {
            return $this->successMessage('Course added successfully' , 201);
        }
        return $this->success($courseData, 200 , 'courses added successfully');
    }
    public function deleteCourse(DeleteCourseRequest $request, CourseService $courseService)
    {
        $courseData = $request->validated();
        $course_id = $courseData['course_id'];
        $course = $courseService->deleteCourse($course_id);
        if(!$course){
            return $this->error('Course not found', 404);
        }
        return $this->successMessage('Course deleted successfully' , 200);
    }
    public function studentCourses(StudentCoursesRequest $request, CourseService $courseService)
    {
        $student_id = $request->validated();
        $courses = $courseService->studentCourses($student_id);
        if(!$courses){
            return $this->error('Courses not found', 404);
        }
        return $this->success($courses, 200 , 'all courses');
    }
}
