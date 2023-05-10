<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserRequest;
use App\Http\Requests\AssignUserToCourseRequest;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UserProfileRequest;
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
    public function listCoursesAssignedToUser($termId,UserService $userService)
    {
        $user_id = auth()->user()->id;
        $user = $userService->listCoursesAssignedToUser($user_id,$termId);
        if ($user) {
            return $this->success($user,200,'User Courses');
        } else {
            return $this->error('User not found', 404);
        }
    }
}
