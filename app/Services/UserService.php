<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseSemesterEnrollment;
use App\Models\CourseUser;
use App\Models\Department;
use App\Models\User;
use Database\Factories\CourseSemesterEnrollmentFactory;
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
        $user = User::find($id);
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
        $user = User::find(auth()->user()->id);
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
    public function editUser($userData)
    {
        $user = User::find($userData['id']);
        if (!$user) {
            return false;
        }
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->national_id = $userData['national_id'];
        $user->is_admin = $userData['is_admin'];
        $user->is_active = $userData['is_active'];
        $user->save();
        return true;
    }
    public function getCoursesInDepartment($department_id)
    {
        $courses = Course::where('department_id',$department_id)->get();
        $department = Department::where('id',$department_id)->first();
        // put department details in courses array
        foreach ($courses as $course){
            $course['department'] = $department;
        }
        return $courses;
    }
    public function assignUserToCourse($user_course)
    {
        $user_c = CourseUser::create($user_course);
        return $user_c;
    }
    public function listCoursesAssignedToUser($user_id){
        $courses = CourseUser::where('user_id',$user_id)->get('course_id');
        $course_data = [];
        foreach ($courses as $course){
            $course_data[] = Course::find($course->course_id);
            $number_of_students = CourseSemesterEnrollment::where('course_id',$course->course_id)->count();
            $course_data[count($course_data)-1]['number_of_students'] = $number_of_students;
        }
        $course_data = collect($course_data)->map(function ($course) {
            return [
                'course_id' => $course->id,
                'course_code' => $course->course_code,
                'course_name' => $course->name,
                'number_of_students' => $course['number_of_students'],
            ];
        });
        return $course_data;
    }

}
