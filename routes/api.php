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
use App\Http\Controllers\ActivityLogController;

Route::post('/login', [AuthController::class, 'login']);
// middle ware for api auth group
Route::Group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // raafa grades
    Route::post('/raafa-grades', [GraphController::class, 'raafaGrades']);
    // apply raafa grades
    Route::post('/apply-raafa-grades', [GraphController::class, 'applyRaafaGrades']);
    // get courses in semester
    Route::get('/courses-in-semester-merge', [CourseController::class, 'getCoursesInSemesterMerge']);
    ///////////////////////////user routes///////////////////////////
    // list courses assigned to user
    Route::get('/list-courses-assigned-to-user', [UserController::class, 'listCoursesAssignedToUser']);
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
    // get all semesters assign to course
    Route::post('/get-course-semesters', [GraphController::class, 'getCourseSemesters']);

    Route::post('add-students-to-course-excel', [CourseGradeController::class, 'addStudentsToCourseExcel']);
    Route::post('delete-student-from-course', [CourseGradeController::class, 'deleteStudentFromCourse']);
    Route::post('delete-all-students-from-course', [CourseGradeController::class, 'deleteAllStudentsFromCourse']);

    Route::post('add-one-student-grade', [CourseGradeController::class, 'addOneStudentGrade']);
    Route::post('add-students-grades-excel', [CourseGradeController::class, 'addStudentsGradesExcel']);
    Route::post('add-students-term-work-excel', [CourseGradeController::class, 'addStudentTermWork']);
    Route::post('add-students-exam-work-excel', [CourseGradeController::class, 'addStudentExamWork']);
    Route::post('add-students-extra-grades-excel', [CourseGradeController::class, 'addStudentExtraGrades']);
    Route::post('delete-course-grades', [CourseGradeController::class, 'deleteCourseGrades']);
    Route::post('export-course-grades', [CourseGradeController::class, 'exportCourseGrades']);
    Route::get('/courses/{course}', [CourseController::class, 'getCourse']);
    Route::post('/edit-course', [CourseController::class, 'editCourse']);
    // get all departments
    Route::get('/departments', [DepartmentController::class, 'getDepartments']);
    Route::get('/list-courses', [CourseController::class, 'listCourses']);
    Route::get('/list-courses-in-semester', [CourseController::class, 'listCoursesInSemester']);
    // get current semester
    Route::get('/current-semester', [UserController::class, 'getCurrentSemester']);

    // activity log
    Route::get('/get-logs', [ActivityLogController::class, 'getLogs']);
    // download file from storage
    Route::get('/get-file/storage/{file_name}', [ActivityLogController::class, 'getFile']);
    // compare courses semesters
    Route::post('/compare-courses-semesters', [GraphController::class, 'compareCoursesSemesters']);

    // student courses
    Route::post('/student-courses', [CourseController::class, 'studentCourses']);
});
// miidle ware isadmin for add user
Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {

    // insert grade
    Route::post('/insert-grade', [CourseGradeController::class, 'insertGrade']);
    // import from execl file courses in database
    Route::post('/import-courses', [CourseController::class, 'importCourses']);
    // add new user
    Route::post('/add-user', [UserController::class, 'addUser']);
    Route::get('/users/{user}', [UserController::class, 'getUser']);
    // assign user to course
    Route::post('/assign-user-to-course', [UserController::class, 'assignUserToCourse']);
    // get all courses in a department
    Route::get('/courses-in-department/{dept_id}', [DepartmentController::class, 'getCoursesInDepartment']);
    // list all users
    Route::get('/list-users', [UserController::class, 'listUsers']);
    // edit user
    Route::post('/edit-user', [UserController::class, 'editUser']);
    // delete user
    Route::delete('/delete-user', [UserController::class, 'deleteUser']);

    // add new semester
    Route::post('/add-semester', [UserController::class, 'addSemester']);
    // get courses in semester
    Route::get('/courses-in-semester/{semesterId}', [UserController::class, 'getCoursesInSemester']);
    // edit course semester
    Route::post('/edit-course-semester', [UserController::class, 'editCourseSemester']);

    Route::post('/add-course', [CourseController::class, 'addCourse']);

    // delete course
    Route::post('/delete-course', [CourseController::class, 'deleteCourse']);
    // add department
    Route::post('/add-department', [DepartmentController::class, 'addDepartment']);
    // delete department
    Route::delete('/delete-department/{id}', [DepartmentController::class, 'deleteDepartment']);
    // edit department
    Route::post('/edit-department', [DepartmentController::class, 'editDepartment']);
});