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
            // set log neame
            $activity = activity()->causedBy(auth()->user())->performedOn($user)->
            withProperties(['old' => null, 'new' => $user])->event('ADD_USER')
            ->log('Add new user');
            $activity->log_name = 'USER';
            $activity->save();
            return true;
        }else{
            return false;
        }
    }
    public function listUsers()
    {
        $users = User::select(['id', 'name', 'email'])->get();
        // dont send the auth user
        $users = $users->filter(function ($value, $key) {
            return $value->id != auth()->user()->id;
        });
        return $users;
    }
    public function deleteUser($id)
    {
        $user = User::find($id);
        if(!$user){
            return false;
        }
        $temp = clone $user;
        $user = $user->delete();
        $activity = activity()->causedBy(auth()->user())->performedOn($temp)->
        withProperties(['old' => $temp, 'new' => null])->event('DELETE_USER')
        ->log('Delete user');
        $activity->log_name = 'USER';
        $activity->save();
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
        $temp = clone $user;
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->national_id = $userData['national_id'];
        $user->is_admin = $userData['is_admin'];
        $user->is_active = $userData['is_active'];
        $user->save();

        $activity = activity()->causedBy(auth()->user())->performedOn($temp)->
        withProperties(['old' => $temp, 'new' => $user])->event('EDIT_USER')
        ->log('Edit user');
        $activity->log_name = 'USER';
        $activity->save();
        
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

        if (!$course_semester) {
            return false;
        }
        
        // add to course user table the user id and course semester id
        $course_user = CourseUser::firstOrCreate([
            'user_id' => $user_course['user_id'],
            'course_semester_id' => $course_semester->id,
            'course_id' => $user_course['course_id'],
            'semester_id' => $semester->id,
        ]);
        if($course_user){
            $activity = activity()->causedBy(auth()->user())->performedOn($course_user)->
            withProperties(['old' => null, 'new' => $course_user])->event('ASSIGN_USER_TO_COURSE')
            ->log('Assign user to course');
            $activity->log_name = 'ASSIGN_USER';
            $activity->save();
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
            $activity = activity()->causedBy(auth()->user())->performedOn($semester)->
            withProperties(['old' => null, 'new' => $semester])->event('ADD_SEMESTER')
            ->log('Add new semester');
            $activity->log_name = 'SEMESTER';
            $activity->save();
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
