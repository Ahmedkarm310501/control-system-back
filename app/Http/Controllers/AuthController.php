<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use App\Traits\HttpResponses;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginRequest $request , AuthService $authService) {
        $token = $authService->login($request->validated());
        if (!$token) {
            return $this->error('invalid credentials', 401);
        }
        return $this->success($token, 200, 'login successful');
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return $this->successMessage('logout successful');
    }


}
