<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Http\Requests\AddCourseRequest;
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

    public function getCourse($course, CourseService $courseService)
    {
        $course = $courseService->getCourse($course);
        if(!$course){
            return $this->error('Course not found', 404);
        } 
        return $this->success($course, 200 , 'course');
    }

}