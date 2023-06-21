<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompareTwoSemesterRequest;
use App\Http\Requests\NumberStudentsRequest;
use App\Http\Requests\RafaaGradesRequest;
use App\Http\Requests\courseSemetersRequest;
use App\Http\Requests\CompareCoursesSemestersRequest;
use App\Http\Requests\ApplyRaafaGradesRequest;
use App\Services\DashboardService;
use App\Traits\HttpResponses;


class GraphController extends Controller
{
    use HttpResponses;
    public function graphTwo(NumberStudentsRequest $request,DashboardService $courseGradeService)
    {
        $course_semester = $request->validated();
        $graph_two = $courseGradeService->graphTwo($course_semester);
        return $this->success($graph_two,200,'Graph two');
    }
    public function graphOne(NumberStudentsRequest $request,DashboardService $courseGradeService)
    {
        $course_semester = $request->validated();
        $graph_one = $courseGradeService->graphOne($course_semester);
        return $this->success($graph_one,200,'Graph one');
    }
    public function graphThree(NumberStudentsRequest $request,DashboardService $courseGradeService)
    {
        $course_semester = $request->validated();
        $graph_three = $courseGradeService->graphThree($course_semester);
        return $this->success($graph_three,200,'Graph three');
    }
    public function graphCompareOne(CompareTwoSemesterRequest $request,DashboardService $courseGradeService)
    {
        $course_semester = $request->validated();
        $graph_compare_one = $courseGradeService->graphCompareOne($course_semester);
        return $this->success($graph_compare_one,200,'Graph compare one');
    }
    public function graphCompareTwo(CompareTwoSemesterRequest $request,DashboardService $courseGradeService)
    {
        $course_semester = $request->validated();
        $graph_compare_two = $courseGradeService->graphCompareTwo($course_semester);
        return $this->success($graph_compare_two,200,'Graph compare two');
    }
    public function graphCompareThree(CompareTwoSemesterRequest $request,DashboardService $courseGradeService)
    {
        $course_semester = $request->validated();
        $graph_compare_three = $courseGradeService->graphCompareThree($course_semester);
        return $this->success($graph_compare_three,200,'Graph compare three');
    }
    public function raafaGrades(RafaaGradesRequest $request,DashboardService $courseGradeService)
    {
        $raafa_details = $request->validated();
        $raafa_update = $courseGradeService->raafaGrades($raafa_details);
        if($raafa_update == false){
            return $this->error('number of grades must not more than 20 grades or course id not found',400);
        }
        return $this->success($raafa_update,200,'graphs updated after adding rafaa grades');
    }
    public function getCourseSemesters(courseSemetersRequest $request,DashboardService $courseGradeService)
    {
        $course_id = $request->validated();
        $course_semesters = $courseGradeService->getCourseSemesters($course_id);
        return $this->success($course_semesters,200,'course semesters');
    }
    public function compareCoursesSemesters(CompareCoursesSemestersRequest $request,DashboardService $courseGradeService)
    {
        $course_semesters = $request->validated();
        $compare_courses_semesters = $courseGradeService->compareCoursesSemesters($course_semesters);
        if($compare_courses_semesters == false){
            return $this->error('course id no assign to semester id',400);
        }
        return $this->success($compare_courses_semesters,200,'compare courses semesters');
    }
    public function applyRaafaGrades(ApplyRaafaGradesRequest $request,DashboardService $courseGradeService)
    {
        $raafa_details = $request->validated();
        $raafa_update = $courseGradeService->applyRaafaGrades($raafa_details);
        if($raafa_update == false){
            return $this->error('course id no assign to semester id',400);
        }
        return $this->successMessage('raafa grades applied successfully',200);
        // return $this->success($raafa_update,200,'graphs updated after applying rafaa grades');
    }
}
