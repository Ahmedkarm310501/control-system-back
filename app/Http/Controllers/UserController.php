<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserRequest;
use App\Services\UserService;
use App\Traits\HttpResponses;
use App\Models\User;
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
    
}
