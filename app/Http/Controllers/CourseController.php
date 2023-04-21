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

}