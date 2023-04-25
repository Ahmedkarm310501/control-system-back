<?php

namespace App\Http\Controllers;

use App\Http\Requests\NumberStudentsRequest;
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
}
