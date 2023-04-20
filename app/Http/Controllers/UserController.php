<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserRequest;
use App\Services\UserService;
use App\Traits\HttpResponses;
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
}
