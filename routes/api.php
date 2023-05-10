<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseGradeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\GraphController;

Route::post('/login', [AuthController::class, 'login']);
// middle ware for api auth group
Route::Group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    ///////////////////////////user routes///////////////////////////
    // list courses assigned to user
    Route::get('/list-courses-assigned-to-user/{termId}', [UserController::class, 'listCoursesAssignedToUser']);
    Route::get('/user-profile', [UserController::class, 'userProfile']);
    Route::post('/update-password', [UserController::class, 'updatePassword']);
    /////////////////////////////////////////////////////////////////
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/course-grades/{courseId}/{termId}', [CourseGradeController::class, 'getCourseGrades']);
    Route::post('add-student-to-course', [CourseGradeController::class, 'addStudentToCourse']);
    //////////////////////////graph one routes///////////////////////////////
    // graph one
    Route::post('/graph-one', [GraphController::class, 'graphOne']);
    //graph two
    Route::post('/graph-two', [GraphController::class, 'graphTwo']);
    //graph three
    Route::post('/graph-three', [GraphController::class, 'graphThree']);
    // graph comapare one courses in two semesters
    Route::post('/graph-one-compare', [GraphController::class, 'graphCompareOne']);
    // graph comapare two courses in two semesters
    Route::post('/graph-two-compare', [GraphController::class, 'graphCompareTwo']);
    // graph comapare three courses in two semesters
    Route::post('/graph-three-compare', [GraphController::class, 'graphCompareThree']);

    Route::post('add-students-to-course-excel', [CourseGradeController::class, 'addStudentsToCourseExcel']);
    Route::post('delete-student-from-course', [CourseGradeController::class, 'deleteStudentFromCourse']);
    Route::post('add-one-student-grade', [CourseGradeController::class, 'addOneStudentGrade']);
    Route::post('add-students-grades-excel', [CourseGradeController::class, 'addStudentsGradesExcel']);
    Route::post('delete-course-grades', [CourseGradeController::class, 'deleteCourseGrades']);
    Route::post('export-course-grades', [CourseGradeController::class, 'exportCourseGrades']);


});
// miidle ware isadmin for add user
Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::post('/add-user',[UserController::class,'addUser']);
    // assign user to course
    Route::post('/assign-user-to-course',[UserController::class,'assignUserToCourse']);
    // get all courses in a department
    Route::get('/courses-in-department/{dept_id}', [DepartmentController::class, 'getCoursesInDepartment']);
    // get all departments
    Route::get('/departments',[DepartmentController::class,'getDepartments']);
    // list all users
    Route::get('/list-users',[UserController::class,'listUsers']);
    // edit user
    Route::post('/edit-user',[UserController::class,'editUser']);
    // delete user
    Route::delete('/delete-user',[UserController::class,'deleteUser']);


    Route::post('/add-course',[CourseController::class,'addCourse']);
    Route::get('/list-courses',[CourseController::class,'listCourses']);
    Route::get('/courses/{course}', [CourseController::class, 'getCourse']);
});

