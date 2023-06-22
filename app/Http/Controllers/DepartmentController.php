<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\Semester;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
class DepartmentController extends Controller
{
    use HttpResponses;
    public function getDepartments()
    {
        $departments = Department::select(['id','dept_code'])->get();
        $semester = Semester::latest()->first();
        if(!$departments){
            return $this->error('No Departments Found',404);
        }
        if(auth()->user()->is_admin ==1){
            return $this->success($departments,200,'all Departments');
        }else{
            $courses_ids = CourseUser::where('user_id', auth()->user()->id)->where('semester_id', $semester->id)->get('course_id');
            $department_ids = Course::whereIn('id', $courses_ids)->get('department_id');
            $departments = Department::whereIn('id', $department_ids)->get();
            return $this->success($departments,200,'all Departments');
        }
    }
    public function getCoursesInDepartment(Request $request,UserService $userService)
    {
        $department_id = $request->dept_id;
        $courses = $userService->getCoursesInDepartment($department_id);
        if(!$courses){
            return $this->error('No Courses Found',404);
        }
        return $this->success($courses,200,'all Courses');
    }
}
