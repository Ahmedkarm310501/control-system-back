<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;


Route::post('/login', [AuthController::class, 'login']);
// middle ware for api auth group
Route::Group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

});
// miidle ware isadmin for add user
Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::post('/add-user',[UserController::class,'addUser']);
    // list all users
    Route::get('/list-users',[UserController::class,'listUsers']);
    
    Route::post('/add-course',[CourseController::class,'addCourse']);
    Route::get('/list-courses',[CourseController::class,'listCourses']);
    Route::get('/courses/{course}', [CourseController::class, 'getCourse']);
});

