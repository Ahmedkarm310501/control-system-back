<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login($userData)
    {
        $user = User::where('email', $userData['email'])->first();
        if (!$user || !Hash::check($userData['password'], $user->password)) {
            throw new \Exception('invalid credentials', 401);
        } else if ($user->is_active == 0) {
            throw new \Exception('user is not active', 403);
        } else {
            $token = $user->createToken($userData['email'])->plainTextToken;
            return [
                'user' => $user,
                'token' => $token
            ];
        }
    }
}