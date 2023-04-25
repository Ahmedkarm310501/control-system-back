<?php

namespace App\Services;

use App\Models\CourseSemesterEnrollment;

class DashboardService
{
    public function part_one($course_id, $semester_id){
        $enrollements = CourseSemesterEnrollment::where('course_id', $course_id)->where('semester_id',$semester_id)->get();
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
    public function part_two($course_id,$semester_id){
        $enrollements = CourseSemesterEnrollment::where('course_id', $course_id)->where('semester_id',$semester_id)->get();
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
    public function part_three($course_id,$semester_id){
        $enrollements = CourseSemesterEnrollment::where('course_id', $course_id)->where('semester_id',$semester_id)->whereRaw('term_work + exam_work < 50')->get();
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
        $number_of_students_40 = 0;
        $number_of_students_41 = 0;
        $number_of_students_42 = 0;
        $number_of_students_43 = 0;
        $number_of_students_44 = 0;
        $number_of_students_45 = 0;
        $number_of_students_46 = 0;
        $number_of_students_47 = 0;
        $number_of_students_48 = 0;
        $number_of_students_49 = 0;
        // get number of students in range of 40-49
        $enrollements_range = CourseSemesterEnrollment::where('course_id',$course_id)->where('semester_id',$semester_id)->whereRaw('term_work + exam_work >= 40')->whereRaw('term_work + exam_work < 50')->get();
        foreach($enrollements_range as $enroll){
            $enroll_grade = $enroll->term_work + $enroll->exam_work;
            // turn it to int
            $enroll_grade = (int)$enroll_grade;
            if($enroll_grade == 40){
                $number_of_students_40++;
            }else if($enroll_grade == 41){
                $number_of_students_41++;
            }else if($enroll_grade == 42){
                $number_of_students_42++;
            }else if($enroll_grade == 43){
                $number_of_students_43++;    
            }else if($enroll_grade == 44){
                $number_of_students_44++;
            }else if($enroll_grade == 45){
                $number_of_students_45++;
            }else if($enroll_grade == 46){
                $number_of_students_46++;
            }else if($enroll_grade == 47){
                $number_of_students_47++;
            }else if($enroll_grade == 48){
                $number_of_students_48++;
            }else if($enroll_grade == 49){
                $number_of_students_49++;
            }   
        }


        $graph_three = [
            'need_one_grade' => $need_one_grade,
            'need_two_grade' => $need_two_grade,
            'need_three_grade' => $need_three_grade,
            'need_four_grade' => $need_four_grade,
            'need_five_grade' => $need_five_grade,
            'number_of_students_40' => $number_of_students_40,
            'number_of_students_41' => $number_of_students_41,
            'number_of_students_42' => $number_of_students_42,
            'number_of_students_43' => $number_of_students_43,
            'number_of_students_44' => $number_of_students_44,
            'number_of_students_45' => $number_of_students_45,
            'number_of_students_46' => $number_of_students_46,
            'number_of_students_47' => $number_of_students_47,
            'number_of_students_48' => $number_of_students_48,
            'number_of_students_49' => $number_of_students_49,
        ];
        return $graph_three;
    }
    public function graphOne($course_semester){
        $graph_one = $this->part_one($course_semester['course_id'], $course_semester['semester_id']);
        return $graph_one;
    }
    public function graphTwo($course_semester){
        $graph_two = $this->part_two($course_semester['course_id'], $course_semester['semester_id']);
        return $graph_two;
    }
    public function graphThree($course_semester){
        $graph_three = $this->part_three($course_semester['course_id'], $course_semester['semester_id']);
        return $graph_three;
    }
    public function graphCompareOne($course_semester){
        $first_semester = $this->part_one($course_semester['course_id'], $course_semester['semester_id_one']);
        $second_semester = $this->part_one($course_semester['course_id'], $course_semester['semester_id_two']);
        $graph_compare_one = [
            'first_semester' => $first_semester,
            'second_semester' => $second_semester,
        ];
        return $graph_compare_one;
    }
    public function graphCompareTwo($course_semester){
        $first_semester = $this->part_two($course_semester['course_id'], $course_semester['semester_id_one']);
        $second_semester = $this->part_two($course_semester['course_id'], $course_semester['semester_id_two']);
        $graph_compare_two = [
            'first_semester' => $first_semester,
            'second_semester' => $second_semester,
        ];
        return $graph_compare_two;
    }
    public function graphCompareThree($course_semester){
        $first_semester = $this->part_three($course_semester['course_id'], $course_semester['semester_id_one']);
        $second_semester = $this->part_three($course_semester['course_id'], $course_semester['semester_id_two']);
        $graph_compare_three = [
            'first_semester' => $first_semester,
            'second_semester' => $second_semester,
        ];
        return $graph_compare_three;
    }
}
