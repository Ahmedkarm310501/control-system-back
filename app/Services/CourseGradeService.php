<?php

namespace App\Services;
use App\Models\CourseSemesterEnrollment;

class CourseGradeService{

    public function getCourseGrades($course_code,$year){


    }
    public function getNumberOfStudents($course_semester){
        $number_of_students = CourseSemesterEnrollment::where('course_id', $course_semester['course_id'])->where('semester_id',$course_semester['semester_id'])->count();
        return $number_of_students;

    }
    public function getAverageGrade($course_semester){
        $enrollements = CourseSemesterEnrollment::where('course_id', $course_semester['course_id'])->where('semester_id',$course_semester['semester_id'])->get();
        $total_grade = 0;
        foreach($enrollements as $enroll){
            $total_grade += $enroll->term_work + $enroll->exam_work;
        }
        $average_grade = $total_grade / count($enrollements);
        //$average_grade = number_format($average_grade, 2);
        return $average_grade;
    }
    public function getNumberOfPassedStudents($course_semester){
        $enrollements = CourseSemesterEnrollment::where('course_id', $course_semester['course_id'])->where('semester_id',$course_semester['semester_id'])->get();
        $passed_students = 0;
        foreach($enrollements as $enroll){
            $total_grade = $enroll->term_work + $enroll->exam_work;
            if($total_grade >= 50){
                $passed_students++;
            }
        }
        return $passed_students;
    }
    public function getNumberOfFailedStudents($course_semester){
        $enrollements = CourseSemesterEnrollment::where('course_id', $course_semester['course_id'])->where('semester_id',$course_semester['semester_id'])->get();
        $failed_students = 0;
        foreach($enrollements as $enroll){
            $total_grade = $enroll->term_work + $enroll->exam_work;
            if($total_grade < 50){
                $failed_students++;
            }
        }
        return $failed_students;
    }

}
