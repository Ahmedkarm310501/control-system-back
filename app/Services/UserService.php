<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
    public function listUsers()
    {
        $users = User::select(['id', 'name', 'email'])->get();
        return $users;
    }
    public function deleteUser($id)
    {
        $user = User::find($id);
        if(!$user){
            return false;
        }
        $user = $user->delete();
        return true;
    }
    public function userProfile($id)
    {
        $user = User::where('id', $id)->first();
        if (!$user) {
            return false;
        }
        $user_profile = [
            'name' => $user->name,
            'email' => $user->email,
            'national_id' => $user->national_id,
        ];
        return $user_profile;
    }
    public function updatePassword($user_pass)
    {
        $user = User::find($user_pass['id']);
        if (!Hash::check($user_pass['current_password'], $user->password)) {
            return false;
        }
        if ($user_pass['new_password'] != $user_pass['new_password_confirmation']) {
            return false;
        }
        $user->password = Hash::make($user_pass['new_password']);
        $user->save();
        return true;
    }
}
