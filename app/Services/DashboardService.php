<?php

namespace App\Services;

use App\Models\CourseSemesterEnrollment;

class DashboardService
{
    public function graphOne($course_semester){
        $enrollements = CourseSemesterEnrollment::where('course_id', $course_semester['course_id'])->where('semester_id',$course_semester['semester_id'])->get();
        $number_of_students = count($enrollements);
        $total_grade = 0;
        $passed_students = 0;
        $failed_students = 0;
        foreach($enrollements as $enroll){
            $total_grade += $enroll->term_work + $enroll->exam_work;
            if($enroll->term_work + $enroll->exam_work >= 50){
                $passed_students++;
            }else{
                $failed_students++;
            }
        }
        $average_grade = $total_grade / count($enrollements);
        $graph_one = [
            'number_of_students' => $number_of_students,
            'average_grade' => $average_grade,
            'passed_students' => $passed_students,
            'failed_students' => $failed_students,
        ];
        return $graph_one;
    }
    public function graphTwo($course_semester){
        $enrollements = CourseSemesterEnrollment::where('course_id', $course_semester['course_id'])->where('semester_id',$course_semester['semester_id'])->get();
        $passed_students = 0;
        $failed_students = 0;
        $grade_A_plus = 0;
        $grade_A = 0;
        $grade_B_plus = 0;
        $grade_B = 0;
        $grade_C_plus = 0;
        $grade_C = 0;
        $grade_D_plus = 0;
        $grade_D = 0;
        $grade_F = 0;
        foreach($enrollements as $enroll){
            if($enroll->term_work + $enroll->exam_work >= 90){
                $passed_students++;
                $grade_A_plus++;
            }else if($enroll->term_work + $enroll->exam_work >= 85){
                $passed_students++;
                $grade_A++;
            }else if($enroll->term_work + $enroll->exam_work >= 80){
                $passed_students++;
                $grade_B_plus++;
            }else if($enroll->term_work + $enroll->exam_work >= 75){
                $passed_students++;
                $grade_B++;
            }else if($enroll->term_work + $enroll->exam_work >= 70){
                $passed_students++;
                $grade_C_plus++;
            }else if($enroll->term_work + $enroll->exam_work >= 65){
                $passed_students++;
                $grade_C++;
            }else if($enroll->term_work + $enroll->exam_work >= 60){
                $passed_students++;
                $grade_D_plus++;
            }else if($enroll->term_work + $enroll->exam_work >= 50){
                $passed_students++;
                $grade_D++;
            }else{
                $failed_students++;
                $grade_F++;
            }
        }
        $perecentage_passed = ($passed_students / count($enrollements)) * 100;
        $perecentage_failed = ($failed_students / count($enrollements)) * 100;
        // make it 2 decimal
        $perecentage_passed = number_format((float)$perecentage_passed, 2, '.', '');
        $perecentage_failed = number_format((float)$perecentage_failed, 2, '.', '');
        // turn it to float
        $perecentage_passed = (float)$perecentage_passed;
        $perecentage_failed = (float)$perecentage_failed;
        $graph_two = [
            'perecentage_passed' => $perecentage_passed,
            'perecentage_failed' => $perecentage_failed,
            'grade_A_plus' => $grade_A_plus,
            'grade_A' => $grade_A,
            'grade_B_plus' => $grade_B_plus,
            'grade_B' => $grade_B,
            'grade_C_plus' => $grade_C_plus,
            'grade_C' => $grade_C,
            'grade_D_plus' => $grade_D_plus,
            'grade_D' => $grade_D,
            'grade_F' => $grade_F,
        ];
        return $graph_two;
    }
    public function graphThree($course_semester){
        // get number of students that need one grade to pass
        $enrollements = CourseSemesterEnrollment::where('course_id', $course_semester['course_id'])->where('semester_id',$course_semester['semester_id'])->whereRaw('term_work + exam_work < 50')->get();
        $need_one_grade = 0;
        $need_two_grade = 0;
        $need_three_grade = 0;
        $need_four_grade = 0;
        $need_five_grade = 0;
        foreach($enrollements as $enroll){
            if(($enroll->term_work + $enroll->exam_work)+1 >= 50){
                $need_one_grade++;
            }else if(($enroll->term_work + $enroll->exam_work)+2 >= 50){
                $need_two_grade++;
            }else if(($enroll->term_work + $enroll->exam_work)+3 >= 50){
                $need_three_grade++;
            }else if(($enroll->term_work + $enroll->exam_work)+4 >= 50){
                $need_four_grade++;
            }else if(($enroll->term_work + $enroll->exam_work)+5 >= 50){
                $need_five_grade++;
            }
        }
        $graph_three = [
            'need_one_grade' => $need_one_grade,
            'need_two_grade' => $need_two_grade,
            'need_three_grade' => $need_three_grade,
            'need_four_grade' => $need_four_grade,
            'need_five_grade' => $need_five_grade,
        ];
        return $graph_three;
    }

}
