<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
class DepartmentController extends Controller
{
    use HttpResponses;
    public function getDepartments()
    {
        $departments = Department::select(['id','dept_code'])->get();
        if(!$departments){
            return $this->error('No Departments Found',404);
        }
        return $this->success($departments,200,'all Departments');
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
