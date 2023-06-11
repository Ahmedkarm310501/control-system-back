<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseSemesterEnrollment;
use App\Models\CourseUser;
use App\Models\Department;
use App\Models\User;
use App\Models\Semester;
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
    public function listCoursesAssignedToUser($user_id ){
        $termId = Semester::latest()->first()->id;
        $courses = CourseUser::where('user_id',$user_id)->where('semester_id',$termId)->with('course')->get();
        // return for each course the course_id, course_code, course_name, number_of_students
        $new_courses = [];
        foreach ($courses as $course){
            $c['course_id'] = $course->course->id;
            $c['course_code'] = $course->course->course_code;
            $c['course_name'] = $course->course->name;
            $c['number_of_students'] = CourseSemesterEnrollment::where('course_semester_id',$course->course_semester_id)->count();
            $c['term_id']= $termId;
            $new_courses[] = $c;
        }
        return $new_courses;
    }

}
