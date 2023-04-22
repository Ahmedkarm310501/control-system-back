<?php

namespace App\Services;
use App\Models\CourseUser;
use App\Models\CourseSemesterEnrollment;
use App\Models\Course;
use App\Models\Semester;
use App\Models\Student;
use Maatwebsite\Excel\Facades\Excel;


class CourseGradeService{

    public function getCourseGrades($course_code, $year, $user) 
    {
        $course = Course::where('course_code', $course_code)->first();
        if(!$course){
            throw new \Exception('Course not found', 404);
        }
        // check if the user has access to the course
        $course_user = CourseUser::where('user_id', $user->id)
            ->where('course_id', $course->id)->first();
        if(!$course_user){
            throw new \Exception('You do not have access to this course', 403);
        }
        // get semester id
        $semester = Semester::where('year', $year)->first();
        if(!$semester){
            throw new \Exception('Semester not found', 404);
        }
        // get course semester enrollment with the semester id and course id 
        $course_semester_enrollment = CourseSemesterEnrollment::with('student:name,id')
            ->where('course_id', $course->id)
            ->where('semester_id', $semester->id)
            ->get()
            ->map(function ($enrollment) {
                if ($enrollment->term_work === null || $enrollment->exam_work === null) {
                    $enrollment->total_grade = null;
                    $enrollment->grade = null;
                } else {
                    $enrollment->total_grade = $enrollment->term_work + $enrollment->exam_work;
                    $enrollment->grade = $this->calcGrade($enrollment->total_grade);
                }
                return $enrollment;
            });

        if($course_semester_enrollment->isEmpty()){
            throw new \Exception('Course has no students enrolled', 404);
        }

        return $course_semester_enrollment;
    }

    public function calcGrade($grade){
        if($grade >= 90)
            return 'A+';
        elseif($grade >= 85)
            return 'A';
        elseif($grade >= 80)    
            return 'B+';
        elseif($grade >= 75)    
            return 'B';
        elseif($grade >= 70)
            return 'C+';
        elseif($grade >= 65)
            return 'C';
        elseif($grade >= 60)
            return 'D+';
        elseif($grade >= 50)
            return 'D';
        else
            return 'F';
    }
    
    public function addStudentToCourse($data , $user)
    {
        $course = Course::find($data['course_id']);
        if(!$course){
            throw new \Exception('Course not found', 404);
        }
        // check if the user has access to the course
        $course_user = CourseUser::where('user_id', $user->id)
        ->where('course_id', $course->id)->first();
        if(!$course_user){
            throw new \Exception('You do not have access to this course', 403);
        }
        // get semster id
        $semester = Semester::find($data['semester_id']);
        if(!$semester){
            throw new \Exception('Semester not found', 404);
        }
        $student = Student::find($data['student_id']);
        if(!$student){
            $student= Student::create([
                'name' => $data['student_name'],
                'id' => $data['student_id'],
            ]);
        }
        $course_semester_enrollment = CourseSemesterEnrollment::firstOrCreate([
            'course_id' => $course->id,
            'semester_id' => $data['semester_id'],
            'student_id' => $student->id,
        ]);
        if($course_semester_enrollment){
            return $course_semester_enrollment;
        }
        throw new \Exception('Error adding student to course', 500);
        
    }

    public function addStudentsToCourseExcel($data , $user)
    {
        $course = Course::find($data['course_id']);
        if(!$course){
            throw new \Exception('Course not found', 404);
        }
        // check if the user has access to the course
        $course_user = CourseUser::where('user_id', $user->id)
        ->where('course_id', $course->id)->first();
        if(!$course_user){
            throw new \Exception('You do not have access to this course', 403);
        }
        // get semster id
        $semester = Semester::find($data['semester_id']);
        if(!$semester){
            throw new \Exception('Semester not found', 404);
        }
        $students = Excel::toArray([], $data['students'])[0];
        $students = array_slice($students, 1);
        $numOfMissingFields = 0;
        foreach($students as $student){
            if(!isset($student[0]) || !isset($student[1])){
                $numOfMissingFields++;
                continue;
            }
            $student = Student::firstOrCreate([
                'id' => $student[0],
                'name' => $student[1],
            ]);
            $course_semester_enrollment = CourseSemesterEnrollment::firstOrCreate([
                'course_id' => $course->id,
                'semester_id' => $data['semester_id'],
                'student_id' => $student->id,
            ]);
        }
        if($course_semester_enrollment){
            return [
                'course_semester_enrollment' => $course_semester_enrollment,
                'numOfMissingFields' => $numOfMissingFields,
            ];
        }
        throw new \Exception('Error adding student to course', 500);
        
    }

}