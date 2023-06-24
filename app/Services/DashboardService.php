<?php

namespace App\Services;

use App\Models\CourseSemesterEnrollment;
use App\Models\CourseSemester;
use App\Models\Course;
use App\Models\Semester;

// impiort db
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function part_one($course_id){

        // get the latest semester id
        $semester_id = Semester::latest()->first()->id;
        $course_semester = CourseSemester::where('course_id', $course_id)->where('semester_id',$semester_id)->first();
        $enrollements = CourseSemesterEnrollment::where('course_semester_id', $course_semester->id)->get();
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
        if ($enrollements->count() == 0) {
            $average_grade = 0;
        } else {
            $average_grade = $total_grade / count($enrollements);
        }
        $graph_one = [
            'number_of_students' => $number_of_students,
            'average_grade' => $average_grade,
            'passed_students' => $passed_students,
            'failed_students' => $failed_students,
        ];
        return $graph_one;
    }
    public function part_two($course_id){
        $semester_id = Semester::latest()->first()->id;
        $course_semester = CourseSemester::where('course_id', $course_id)->where('semester_id',$semester_id)->first();
        $enrollements = CourseSemesterEnrollment::where('course_semester_id', $course_semester->id)->get();
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
        if ($enrollements->count() == 0) {
            $perecentage_passed = 0;
            $perecentage_failed = 0;
        } else {
            $perecentage_passed = ($passed_students / count($enrollements)) * 100;
            $perecentage_failed = ($failed_students / count($enrollements)) * 100;
        }
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
    public function part_three($course_id){
        $semester_id = Semester::latest()->first()->id;
        $course_semester = CourseSemester::where('course_id', $course_id)->where('semester_id',$semester_id)->first();
        $enrollements = CourseSemesterEnrollment::where('course_semester_id', $course_semester->id)->whereRaw('term_work + exam_work < 50')->get();
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
        $enrollements_range = CourseSemesterEnrollment::where('course_semester_id', $course_semester->id)->whereRaw('term_work + exam_work >= 40')->whereRaw('term_work + exam_work < 50')->get();
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
        $graph_one = $this->part_one($course_semester['course_id']);
        return $graph_one;
    }
    public function graphTwo($course_semester){
        $graph_two = $this->part_two($course_semester['course_id']);
        return $graph_two;
    }
    public function graphThree($course_semester){
        $graph_three = $this->part_three($course_semester['course_id']);
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
    public function raafaGrades($raafa_details){
        $semester = Semester::latest()->first();
        $course = Course::find($raafa_details['course_id']);
        if(!$course){
            return false;
        }
        $course_semester_id = CourseSemester::where('course_id', $raafa_details['course_id'])->where('semester_id', $semester->id)->first()->id;
        $enrollments = CourseSemesterEnrollment::where('course_semester_id', $course_semester_id)->get();
        $number_of_passed_students = 0;
        $number_of_failed_students = 0;
        $grade_A_plus = 0;
        $grade_A = 0;
        $grade_B_plus = 0;
        $grade_B = 0;
        $grade_C_plus = 0;
        $grade_C = 0;
        $grade_D_plus = 0;
        $grade_D = 0;
        $grade_F = 0;
        if($raafa_details['number_of_grades'] >20){
            return false;
        }
        foreach($enrollments as $enrollment){
            $total_grade = $enrollment->term_work + $enrollment->exam_work;
            if($total_grade < 50){
                if(($total_grade + $raafa_details['number_of_grades']) >= 50){
                    $number_of_passed_students++;
                }else{
                    $number_of_failed_students++;
                }
            }else{
                $number_of_passed_students++;
            }
        }
        if($raafa_details['AllOrFailed']==0){
            foreach($enrollments as $enroll){
                if($enroll->term_work + $enroll->exam_work >= 90){
                    $grade_A_plus++;
                }else if($enroll->term_work + $enroll->exam_work >= 85){
                    $grade_A++;
                }else if($enroll->term_work + $enroll->exam_work >= 80){
                    $grade_B_plus++;
                }else if($enroll->term_work + $enroll->exam_work >= 75){
                    $grade_B++;
                }else if($enroll->term_work + $enroll->exam_work >= 70){
                    $grade_C_plus++;
                }else if($enroll->term_work + $enroll->exam_work >= 65){
                    $grade_C++;
                }else if($enroll->term_work + $enroll->exam_work >= 60){
                    $grade_D_plus++;
                }else if($enroll->term_work + $enroll->exam_work >= 50){
                    $grade_D++;
                }else{
                    if(($enroll->term_work + $enroll->exam_work + $raafa_details['number_of_grades']) >= 50){
                        $grade_D++;
                    }else{
                        $grade_F++;
                    }
                }
            }
        }else{
            foreach($enrollments as $enroll){
                if($enroll->term_work + $enroll->exam_work + $raafa_details['number_of_grades'] >= 90){
                    $grade_A_plus++;
                }else if($enroll->term_work + $enroll->exam_work + $raafa_details['number_of_grades']  >= 85){
                    $grade_A++;
                }else if($enroll->term_work + $enroll->exam_work + $raafa_details['number_of_grades'] >= 80){
                    $grade_B_plus++;
                }else if($enroll->term_work + $enroll->exam_work + $raafa_details['number_of_grades'] >= 75){
                    $grade_B++;
                }else if($enroll->term_work + $enroll->exam_work + $raafa_details['number_of_grades'] >= 70){
                    $grade_C_plus++;
                }else if($enroll->term_work + $enroll->exam_work + $raafa_details['number_of_grades'] >= 65){
                    $grade_C++;
                }else if($enroll->term_work + $enroll->exam_work + $raafa_details['number_of_grades'] >= 60){
                    $grade_D_plus++;
                }else if($enroll->term_work + $enroll->exam_work + $raafa_details['number_of_grades'] >= 50){
                    $grade_D++;
                }else{
                    if(($enroll->term_work + $enroll->exam_work + $raafa_details['number_of_grades']) >= 50){
                        $grade_D++;
                    }else{
                        $grade_F++;
                    }
                }
            }
        }
        if ($enrollments->count() == 0) {
            $perecentage_passed = 0;
            $perecentage_failed = 0;
        } else {
            $perecentage_passed = ($number_of_passed_students / count($enrollments)) * 100;
            $perecentage_failed = ($number_of_failed_students/ count($enrollments)) * 100;
        }
        // make it 2 decimal
        $perecentage_passed = number_format((float)$perecentage_passed, 2, '.', '');
        $perecentage_failed = number_format((float)$perecentage_failed, 2, '.', '');
        // turn it to float
        $perecentage_passed = (float)$perecentage_passed;
        $perecentage_failed = (float)$perecentage_failed;

        return [
            'number_of_passed_students' => $number_of_passed_students,
            'number_of_failed_students' => $number_of_failed_students,
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
    }
    public function getCourseSemesters($course_id){
        $course_semesters = CourseSemester::where('course_id', $course_id)->get();
        $semester_ids = [];
        foreach($course_semesters as $course_semester){
            array_push($semester_ids, $course_semester->semester_id);
        }
        $semesters = Semester::whereIn('id', $semester_ids)->get();
        $year_terms = [];
        foreach($semesters as $semester){
            $year_term = [
                'id' => $semester->id,
                'year_term' => $semester->year_term = $semester->year . '-' . $semester->term,
            ];
            
            $year_terms []=$year_term;
        }
        return $year_terms;
    }
    public function compareCoursesSemesters($courses_semsesters_ids){
        // check that the course id assign to semester id in table course_semester

        // $course_semester_one = CourseSemester::where('course_id', $courses_semsesters_ids['course_id_one'])->where('semester_id', $courses_semsesters_ids['semester_id_one'])->first();
        // if(!$course_semester_one){
        //     return response()->json(['error' => 'course id one not assign to semester id one'], 400);
        // }
        // $course_semester_two = CourseSemester::where('course_id', $courses_semsesters_ids['course_id_two'])->where('semester_id', $courses_semsesters_ids['semester_id_two'])->first();
        // if(!$course_semester_two){
        //     return response()->json(['error' => 'course id two not assign to semester id two'], 400);
        // }
        // get course code from table course
        $course_one = Course::where('id', $courses_semsesters_ids['course_id_one'])->first();
        $course_two = Course::where('id', $courses_semsesters_ids['course_id_two'])->first();
        $course_semester_one = CourseSemester::where('course_id', $courses_semsesters_ids['course_id_one'])->where('semester_id', $courses_semsesters_ids['semester_id_one'])->first();
        if(!$course_semester_one){
            return false;
        }
        $course_semester_two = CourseSemester::where('course_id', $courses_semsesters_ids['course_id_two'])->where('semester_id', $courses_semsesters_ids['semester_id_two'])->first();
        if(!$course_semester_two){
            return false;
        }
        $first_graph_one = $this->part_one($courses_semsesters_ids['course_id_one'], $courses_semsesters_ids['semester_id_one']);
        $first_graph_two = $this->part_one($courses_semsesters_ids['course_id_two'], $courses_semsesters_ids['semester_id_two']);
        $second_graph_one = $this->part_two($courses_semsesters_ids['course_id_one'], $courses_semsesters_ids['semester_id_one']);
        $second_graph_two = $this->part_two($courses_semsesters_ids['course_id_two'], $courses_semsesters_ids['semester_id_two']);
        return [
            'first_graph_one' => $first_graph_one,
            'first_graph_two' => $first_graph_two,
            'second_graph_one' => $second_graph_one,
            'second_graph_two' => $second_graph_two,
            'course_code_one' => $course_one->name,
            'course_code_two' => $course_two->name,
        ];
    }
    public function applyRaafaGrades($raafa_details){
        if($raafa_details['number_of_gardes'] >20){
            return false;
        }
        $semester = Semester::latest()->first();
        $course_semester_id = CourseSemester::where('course_id', $raafa_details['course_id'])->where('semester_id', $semester->id)->first()->id;
        if($raafa_details['AllOrfFailed'] == 0){
            $enrollments = CourseSemesterEnrollment::where('course_semester_id', $course_semester_id)->whereRaw('term_work + exam_work < 50')
            ->update(['exam_work' => DB::raw('exam_work + ' . $raafa_details['number_of_gardes'])]);
        }else{
            DB::table('course_semester_enrollments')
            ->where('course_semester_id', $course_semester_id)
            ->update([
                'exam_work' => DB::raw("CASE 
                WHEN (term_work + exam_work + {$raafa_details['number_of_gardes']}) <= 100  AND (exam_work + {$raafa_details['number_of_gardes']}) < 60 THEN (exam_work + {$raafa_details['number_of_gardes']})
                ELSE 60
                END")
            ]);
        }
        
        return true;
    }
}
