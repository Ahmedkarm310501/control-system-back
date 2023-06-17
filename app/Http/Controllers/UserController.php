<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddSemesterRequest;
use App\Http\Requests\AddUserRequest;
use App\Http\Requests\AssignUserToCourseRequest;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\EditCourseRequest;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UserProfileRequest;
use App\Models\Semester;
use App\Services\UserService;
use App\Traits\HttpResponses;
use App\Models\User;
use GuzzleHttp\Psr7\Request;

class UserController extends Controller
{
    use HttpResponses;
    public function addUser(AddUserRequest $request, UserService $userService)
    {
        $userData = $request->validated();
        $user = $userService->addUser($userData);
        if ($user) {
            return $this->successMessage('User added successfully');
        } else {
            return $this->error('User not added', 422);
        }
    }


    public function listUsers(UserService $userService)
    {
        $users = $userService->listUsers();
        return $this->success($users,200,'all Users');
    }
    public function deleteUser(UserService $userService ,DeleteUserRequest $request)
    {
        $userData = $request->validated();
        $id = $userData['id'];
        $user = $userService->deleteUser($id);
        if ($user) {
            return $this->successMessage('User deleted successfully');
        } else {
            return $this->error('User not found', 404);
        }
    }
    public function userProfile(UserService $userService)
    {
        $user_id = auth()->user()->id;
        $user = $userService->userProfile($user_id);
        if ($user) {
            return $this->success($user,200,'User Profile');
        } else {
            return $this->error('User not found', 404);
        }
    }
    public function updatePassword(UpdatePasswordRequest $request, UserService $userService)
    {
        $user_pass = $request->validated();
        $user = $userService->updatePassword($user_pass);
        if ($user) {
            return $this->successMessage('Password updated successfully');
        } else {
            return $this->error('Password not updated may be current password not correct or new_password not match with confirmation_password', 403);
        }
    }
    public function editUser(EditUserRequest $request, UserService $userService)
    {
        $userData = $request->validated();
        $user = $userService->editUser($userData);
        if ($user) {
            return $this->successMessage('User edit successfully');
        } else {
            return $this->error('User not found', 404);
        }
    }
    public function assignUserToCourse(AssignUserToCourseRequest $request, UserService $userService)
    {
        $userData = $request->validated();
        $user = $userService->assignUserToCourse($userData);
        if ($user) {
            return $this->successMessage('User assigned to course successfully');
        } else {
            return $this->error('user not assign', 404);
        }
    }
    public function listCoursesAssignedToUser(UserService $userService)
    {
        $user_id = auth()->user()->id;
        $courses = $userService->listCoursesAssignedToUser($user_id);
        if (count($courses) > 0) {
            return $this->success($courses,200,'User Courses');
        } else if (count($courses) == 0) {
            return $this->success($courses,200,'User Courses');
        }
        else {
            return $this->error('User not found', 404);
        }
    }
    public function addSemester(AddSemesterRequest $request , UserService $userService)
    {
        $semesterData = $request->validated();
        $semester = $userService->addSemester($semesterData);
        if ($semester) {
            return $this->successMessage('Semester added successfully');
        } else {
            return $this->error('Semester not added', 422);
        }
    }
    public function getCurrentSemester(){
        // get last semester
        $semester = Semester::orderBy('id', 'desc')->first();
        if (!$semester) {
            return $this->error('Semester not found', 404);
        }
        return $this->success($semester,200,'Current Semester');

    }
    public function getCoursesInSemester($semesterId,UserService $userService){
        $courses = $userService->getCoursesInSemester($semesterId);
        if (!$courses) {
            return $this->error('Courses not found', 404);
        }
        return $this->success($courses,200,'Courses in semester');
    }
    public function editCourseSemester(EditCourseRequest $request, UserService $userService)
    {
        $userData = $request->validated();
        $user = $userService->editCourseSemester($userData['course_ids']);
        if ($user) {
            return $this->successMessage('Course edit successfully');
        } else {
            return $this->error('Course not found', 404);
        }
    }
    public function getUser($id ){
        $user = User::find($id);
        if (!$user) {
            return $this->error('User not found', 404);
        }
        return $this->success($user,200,'User');
    }
}
