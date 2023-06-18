<?php

namespace App\Services;
use App\Models\CourseSemesterEnrollment;



use App\Models\CourseUser;
use App\Models\Course;
use App\Models\Semester;
use App\Models\Student;
use App\Models\CourseSemester;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CourseNamesExport;
use Illuminate\Support\Facades\Storage;



class CourseGradeService{

    public function getCourseGrades($courseId, $termId)
    {
        $course = Course::find($courseId);
        
        if(!$course){
            throw new \Exception('Course not found', 404);
        }
        // check if the user has access to the course
        $course_user = CourseUser::where('user_id', auth()->user()->id)
            ->where('course_id', $course->id)->first();
        
        if(!$course_user){
            throw new \Exception('You do not have access to this course', 403);
        }
        // get semester id
        $semester = CourseSemester::where('course_id', $course->id)
            ->where('semester_id', $termId)->first();
        if(!$semester){
            throw new \Exception('Semester not found', 404);
        }
        // get course semester enrollment with the semester id and course id
        $course_semester_enrollment = CourseSemesterEnrollment::with('student:name,id')
            ->where('course_semester_id', $semester->id)
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
        ->where('course_id', $course->id)->where('semester_id', $data['semester_id'])->first();
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
        $course_semester = CourseSemester::where('course_id', $course->id)->where('semester_id', $semester->id)->first();
        $course_semester_enrollment = CourseSemesterEnrollment::firstOrCreate([
            'course_semester_id' => $course_semester->id,
            'student_id' => $student->id,
        ]);
        if($course_semester_enrollment){
            $activity = activity()->causedBy(auth()->user())->performedOn($course_semester_enrollment)->
            withProperties(['old' => null, 'new' => $course_semester_enrollment])->event('ADD_STUDENT_TO_COURSE')
            ->log('Added student to course');
            $activity->log_name = 'COURSE_NAME';
            $activity->save();
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
        ->where('course_id', $course->id)->where('semester_id', $data['semester_id'])->first();
        if(!$course_user){
            throw new \Exception('You do not have access to this course', 403);
        }
        // get semster id
        $semester = Semester::find($data['semester_id']);
        if(!$semester){
            throw new \Exception('Semester not found', 404);
        }
        $course_semester = CourseSemester::where('course_id', $course->id)->where('semester_id', $semester->id)->first();
        $students = Excel::toArray([], $data['students'])[0];
        $students = array_slice($students, 1);
        $numOfMissingFields = 0;
        $studentsRes = [];
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
                'course_semester_id' => $course_semester->id,
                'student_id' => $student->id,
            ]);
            $studentsRes[] = [
                'student_id' => $student->id,
                'student' => [
                    'name' => $student->name,
                ]
            ];
        }
        $file = $data['students'];
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        // Store the file in the storage/app/public directory
        $file->storeAs('public', $filename);
        // Retrieve the file path
        $filePath = Storage::url($filename);
        
        
        $activity=activity()->causedBy($user)->performedOn($course_semester)
        ->withProperties(['old' => $course_semester->stud_names, 'new' => $filePath])
        ->event('ADD_STUDENTS_TO_COURSE_EXCEL')
        ->log('Added students to course');
        $activity->log_name = 'COURSE_NAMES';
        $activity->save();

        $course_semester->stud_names = $filePath;
        $course_semester->save();
        if(count($studentsRes) > 0){ 
            return [
                'students' => $studentsRes,
                'numOfMissingFields' => $numOfMissingFields,
                'filePath' => $filePath,
            ];
        }
        throw new \Exception('Error adding student to course', 500);

    }

    public function deleteStudentFromCourse($data )
    {
        $course = Course::find($data['course_id']);
        if(!$course){
            throw new \Exception('Course not found', 404);
        }
        // check if the user has access to the course
        $course_user = CourseUser::where('user_id', auth()->user()->id)
        ->where('course_id', $course->id)->where('semester_id', $data['semester_id'])->first();
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
            throw new \Exception('Student not found', 404);
        }
        $course_semester = CourseSemester::where('course_id', $course->id)->where('semester_id', $semester->id)->first();

        $course_semester_enrollment = CourseSemesterEnrollment::
            where('course_semester_id', $course_semester->id)
            ->where('student_id', $student->id);
        $temp = clone $course_semester_enrollment->first();
        $course_semester_enrollment->delete();
        if($course_semester_enrollment){
            $activity=activity()->causedBy(auth()->user())->performedOn($course_semester)
            ->withProperties(['old' => $temp, 'new' => null])
            ->event('DELETE_STUDENT_FROM_COURSE')
            ->log('Deleted student from course');
            $activity->log_name = 'COURSE_NAME';
            $activity->save();

            return $course_semester_enrollment;
        }
        throw new \Exception('Error deleting student from course', 500);

    }

    public function deleteAllStudentsFromCourse($data ){
        $course = Course::find($data['course_id']);
        if(!$course){
            throw new \Exception('Course not found', 404);
        }
        // check if the user has access to the course
        $course_user = CourseUser::where('user_id', auth()->user()->id)
        ->where('course_id', $course->id)->where('semester_id', $data['semester_id'])->first();
        if(!$course_user){
            throw new \Exception('You do not have access to this course', 403);
        }
        // get semster id
        $semester = Semester::find($data['semester_id']);
        if(!$semester){
            throw new \Exception('Semester not found', 404);
        }
        $course_semester = CourseSemester::where('course_id', $course->id)->where('semester_id', $semester->id)->first();

        $course_semester_enrollment = CourseSemesterEnrollment::with('student:name,id')->
            where('course_semester_id', $course_semester->id);

        $temp = clone $course_semester_enrollment;

        $Students = [];
        foreach($course_semester_enrollment->get() as $enrollment){
            $Student = [];
            $Student[] = $enrollment->student->id;
            $Student[] = $enrollment->student->name;
            $Students[] = $Student;
        }

        $filename = uniqid() . '.' .'xlsx';
        Excel::store(new CourseNamesExport($Students), $filename, 'public');
        $filePath = Storage::url($filename);

        
        
        
        $course_semester_enrollment->delete();
        if($course_semester_enrollment){
            $activity=activity()->causedBy(auth()->user())->performedOn($course_semester)
            ->withProperties(['old' => $filePath, 'new' => null])
            ->event('DELETE_ALL_STUDENTS_FROM_COURSE')
            ->log('Deleted all students from course');
            $activity->log_name = 'COURSE_NAME';
            $activity->save();
            return $course_semester_enrollment;
        }
        throw new \Exception('Error deleting student from course', 500);
    }

    public function addOneStudentGrade($data)
    {
        $course = Course::find($data['course_id']);
        if(!$course){
            throw new \Exception('Course not found', 404);
        }
        // check if the user has access to the course
        $course_user = CourseUser::where('user_id', auth()->user()->id)
        ->where('course_id', $course->id)->where('semester_id', $data['semester_id'])->first();
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
            throw new \Exception('Student not found', 404);
        }
        $course_semester = CourseSemester::where('course_id', $course->id)->where('semester_id', $semester->id)->first();
        $course_semester_enrollment = CourseSemesterEnrollment::
            where('course_semester_id', $course_semester->id)
            ->where('student_id', $student->id)
            ->first();

        if(!$course_semester_enrollment){
            throw new \Exception('Student not enrolled in this course', 404);
        }
        // check if stud term work between 0 and 40 and exam work between 0 and 60
        if($data['term_work'] < 0 || $data['term_work'] > 40 || $data['exam_work'] < 0 || $data['exam_work'] > 60){
            throw new \Exception('Term work must be between 0 and 40 and exam work must be between 0 and 60', 400);
        }
        CourseSemesterEnrollment::
            where('course_semester_id', $course_semester->id)
            ->where('student_id', $student->id)
            ->update([
                'term_work' => $data['term_work'],
                'exam_work' => $data['exam_work'],
            ]);
        if($course_semester_enrollment){
            return $course_semester_enrollment;
        }
        throw new \Exception('Error updating student grade', 500);

    }


    public function deleteCourseGrades($data)
    {
        $course = Course::find($data['course_id']);
        if(!$course){
            throw new \Exception('Course not found', 404);
        }
        // check if the user has access to the course
        $course_user = CourseUser::where('user_id', auth()->user()->id)
        ->where('course_id', $course->id)->where('semester_id', $data['semester_id'])->first();
        if(!$course_user){
            throw new \Exception('You do not have access to this course', 403);
        }
        // get semster id
        $semester = Semester::find($data['semester_id']);
        if(!$semester){
            throw new \Exception('Semester not found', 404);
        }
        $course_semester = CourseSemester::where('course_id', $course->id)->where('semester_id', $semester->id)->first();
        $course_semester_enrollment = CourseSemesterEnrollment::
            where('course_semester_id', $course_semester->id)
            ->update([
                'term_work' => null,
                'exam_work' => null,
            ]);
        if($course_semester_enrollment){
            return $course_semester_enrollment;
        }
        throw new \Exception('Error deleting course grades', 500);

    }

    public function addStudentsGradesExcel($data)
    {
        $course = Course::find($data['course_id']);
        if(!$course){
            throw new \Exception('Course not found', 404);
        }
        // check if the user has access to the course
        $course_user = CourseUser::where('user_id', auth()->user()->id)
        ->where('course_id', $course->id)->where('semester_id', $data['semester_id'])->first();
        if(!$course_user){
            throw new \Exception('You do not have access to this course', 403);
        }
        // get semster id
        $semester = Semester::find($data['semester_id']);
        if(!$semester){
            throw new \Exception('Semester not found', 404);
        }
        $course_semester = CourseSemester::where('course_id', $course->id)->where('semester_id', $semester->id)->first();
        $students = Excel::toArray([], $data['students'])[0];
        $students = array_slice($students, 1);
        $wrongFormat = [];
        $index = 1;
        foreach($students as $student){
            if(!isset($student[0]) || !isset($student[1]) || !isset($student[2])){
                $wrongFormat[] = $index;
                $index++;
                continue;
            }
            // check if stud term work between 0 and 40 and exam work between 0 and 60
            if($student[1] < 0 || $student[1] > 40 || $student[2] < 0 || $student[2] > 60){
                $wrongFormat[] = $index;
                $index++;
                continue;
            }
            $course_enrollment = CourseSemesterEnrollment::
            where('course_semester_id', $course_semester->id)
            ->where('student_id', $student[0])
            ->update([
                'term_work' => $student[1],
                'exam_work' => $student[2],
            ]);
            $index++;
        }
        $course_semester_enrollment = CourseSemesterEnrollment::with('student:name,id')
        ->where('course_semester_id', $course_semester->id)
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
        $studWithNoGrade = false;
        foreach($course_semester_enrollment as $enrollment){
            if ($enrollment->term_work === null || $enrollment->exam_work === null) {
                $studWithNoGrade = true;
            }
        }
        // print($course_semester_enrollment);
        if($course_semester_enrollment){
            return [
                'course_semester_enrollment' => $course_semester_enrollment,
                'studWithNoGrade' => $studWithNoGrade,
                'wrongFormat' => $wrongFormat,
            ];
        }
        throw new \Exception('Error adding student to course', 500);

    }


    public function exportCourseGrades($data)
    {
        $course = Course::find($data['course_id']);
        if(!$course){
            throw new \Exception('Course not found', 404);
        }
        // check if the user has access to the course
        $course_user = CourseUser::where('user_id', auth()->user()->id)
        ->where('course_id', $course->id)->where('semester_id', $data['semester_id'])->first();
        if(!$course_user){
            throw new \Exception('You do not have access to this course', 403);
        }
        // get semster id
        $semester = Semester::find($data['semester_id']);
        if(!$semester){
            throw new \Exception('Semester not found', 404);
        }
        $course_semester = CourseSemester::where('course_id', $course->id)->where('semester_id', $semester->id)->first();
        $courseGrades = [];
        $course_semester_enrollment = CourseSemesterEnrollment::with('student:name,id')
        ->where('course_semester_id', $course_semester->id)
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
        foreach($course_semester_enrollment as $enrollment){
            $courseGrade = [];
            $courseGrade[] = $enrollment->student->id;
            $courseGrade[] = $enrollment->student->name;
            $courseGrade[] = $enrollment->term_work;
            $courseGrade[] = $enrollment->exam_work;
            $courseGrade[] = $enrollment->total_grade;
            $courseGrade[] = $enrollment->grade;
            $courseGrades[] = $courseGrade;
        }
        // print($courseGrades);

        if(count ($courseGrades) > 0){
            return $courseGrades;
        }
        throw new \Exception('Error exporting course grades', 500);

    }

}
