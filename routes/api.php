<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseGradeController;


Route::post('/login', [AuthController::class, 'login']);
// middle ware for api auth group
Route::Group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/course-grades/{course_code}/{year}', [CourseGradeController::class, 'getCourseGrades']);
    Route::post('add-student-to-course', [CourseGradeController::class, 'addStudentToCourse']);
    Route::post('add-students-to-course-excel', [CourseGradeController::class, 'addStudentsToCourseExcel']);
    

});
// miidle ware isadmin for add user
Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::post('/add-user',[UserController::class,'addUser']);
    // list all users
    Route::get('/list-users',[UserController::class,'listUsers']);
    // delete user
    Route::delete('/delete-user',[UserController::class,'deleteUser']);

    
    Route::post('/add-course',[CourseController::class,'addCourse']);
    Route::get('/list-courses',[CourseController::class,'listCourses']);
    Route::get('/courses/{course}', [CourseController::class, 'getCourse']);
});
Route::post('/user-profile', [UserController::class, 'userProfile']);
Route::post('update-password', [UserController::class, 'updatePassword']);


