<?php

namespace App\Services;
use App\Models\User;
class UserService
{
    public function addUser($userData)
    {
        $userData['password'] = bcrypt($userData['password']);
        $user = User::create($userData);

        if($user){
            return true;
        }else{
            return false;
        }
    }

}
