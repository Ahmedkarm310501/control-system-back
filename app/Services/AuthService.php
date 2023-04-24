<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class AuthService
{
    public function login($userData)
    {
        $user = User::where('email', $userData['email'])->first();
        if (! $user || ! Hash::check($userData['password'], $user->password)) {
            return null;
        }else{
            $token = $user->createToken($userData['email'])->plainTextToken;
            return [
                'user' => $user,
                'token' => $token
            ];
        }
    }
}