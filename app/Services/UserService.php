<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseSemester;
use App\Models\CourseSemesterEnrollment;
use App\Models\CourseUser;
use App\Models\Department;
use App\Models\Semester;
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
        $department = Department::where('id',$department_id)->get();
        // put department details in courses array
        foreach ($courses as $course){
            $course['department'] = $department;
        }
        return $courses;
    }
    public function assignUserToCourse($user_course)
    {
        // get the leatest semester from table semesters
        $semester = Semester::latest()->first();
        
        // get the id from course semester by course id and semester id
        $course_semester = CourseSemester::where('course_id',$user_course['course_id'])
            ->where('semester_id',$semester->id)->first();
        
        // add to course user table the user id and course semester id
        $course_user = CourseUser::create([
            'user_id' => $user_course['user_id'],
            'course_semester_id' => $course_semester->id,
            'course_id' => $user_course['course_id'],
            'semester_id' => $semester->id,
        ]);
        if($course_user){
            return true;
        }else{
            return false;
        }
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
    public function addSemester($semesterData)
    {
        $semester = Semester::create($semesterData);
        if($semester){
            return true;
        }else{
            return false;
        }
    }
    public function getCoursesInSemester($semester_id)
    {
        // get the courses id from course semester table by semester id
        $courses_id = CourseSemester::where('semester_id',$semester_id)->get('course_id');
        $courses = [];
        foreach ($courses_id as $course_id){
            $courses[] = Course::find($course_id->course_id);
        }
        return $courses;
    }
    public function editCourseSemester($courses)
    {
        // get leatest semester
        $semester_id = Semester::orderBy('id','desc')->first()->id;
        // get all courses in semester
        $courses_in_semester = CourseSemester::where('semester_id',$semester_id)->get('course_id');
        // check if $courses_in_semester is empty create new course semester
        if(count($courses_in_semester) == 0){
            foreach ($courses as $course){
                CourseSemester::create([
                    'course_id' => $course,
                    'semester_id' => $semester_id,
                ]);
            }
            return true;
        }else{
            // delete all courses in semester
            foreach ($courses_in_semester as $course){
                $course_semester = CourseSemester::where('course_id',$course->course_id)
                    ->where('semester_id',$semester_id)->first();
                $course_semester->delete();
            }
            // add new courses in semester
            foreach ($courses as $course){
                CourseSemester::create([
                    'course_id' => $course,
                    'semester_id' => $semester_id,
                ]);
            }
            return true;
        }
    }
}
