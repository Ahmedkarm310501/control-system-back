<?php

namespace App\Services;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Course;
use App\Models\CourseSemester;
use App\Models\Semester;
use App\Models\Department;
use App\Models\CourseRule;
use App\Models\CourseUser;
use App\Models\CourseSemesterEnrollment;
use App\Models\Student;
class CourseService
{

    public function addCourse($courseData){
        // get the department id
        $department = Department::where('dept_code', $courseData['dept_code'])->first();
        if(!$department){
            return false;
        }
        $course = new Course();
        $course->course_code = $courseData['course_code'];
        $course->name = $courseData['course_name'];
        $course->department_id = $department->id;
        $course->course_rule_id = CourseRule::factory()->create()->id;
        $course->save();

        $activity = activity()->causedBy(auth()->user())->performedOn($course)->
            withProperties(['old' => null, 'new' => $course])->event('ADD_COURSE')
            ->log('Add new course with id: '.$course->course_code.'' . ' and name: ' . $course->name . '');
            $activity->log_name = 'COURSE';
            $activity->save();

        return $course;
    }

    public function listCourses(){
        $courses = Course::with('department')->get();
        $semester = Semester::latest()->first();
        if(!$courses ){
            return false;
        }
        if(auth()->user()->is_admin ==1){
            return $courses;
        }else{
            $courses_ids = CourseUser::where('user_id', auth()->user()->id)->where('semester_id', $semester->id)->get('course_id');
            $courses = Course::with('department')->whereIn('id', $courses_ids)->get();
            return $courses;
        }

    }
    public function listCoursesInSemester(){
        $semester = Semester::latest()->first();
        $course_semester = CourseSemester::where('semester_id', $semester->id)->get('course_id');
        $courses = Course::with('department')->whereIn('id', $course_semester)->get();
        if(!$courses ){
            return false;
        }
        if(auth()->user()->is_admin ==1){
            return $courses;
        }else{
            $courses_ids = CourseUser::where('user_id', auth()->user()->id)->where('semester_id', $semester->id)->get('course_id');
            $courses = Course::with('department')->whereIn('id', $courses_ids)->get();
            return $courses;
        }

    }

    public function getCourse($course){

        $course = Course::find($course);
        $rule = $course->rule;
        $department = Department::find($course->department_id);

        if(!$course){
            return false;
        }

        $course['deptName']  = $department->name;
        $course['rule']  = $rule;
        $res ;
        $res['courseID'] = $course->course_code;
        $res['courseName'] = $course->name;
        $res['termWork'] = $course->rule->term_work;
        $res['examWork'] = $course->rule->exam_work;
        $res['department'] = $department->dept_code;
        $res['deptName'] = $department->name;
        $res['instructor'] = $course->rule->instructor;
        $res['totalGrade'] = $course->rule->total;
        return $res;
    }

    public function editCourse($courseData){
        if($courseData['term_work'] + $courseData['exam_work'] != $courseData['total']){
            throw new \Exception('Term work + exam work must = total', 403);
        }
        $course = Course::find($courseData['course_id']);
        if(!$course){
            return false;
        }
        $course_user = CourseUser::where('course_id', $courseData['course_id'])
        ->where('semester_id', $courseData['semester_id'])->where('user_id', auth()->user()->id)->first();
        if(!$course_user && auth()->user()->is_admin != 1){
            return false;
        }
        $department = Department::where('dept_code', $courseData['dept_code'])->first();
        if(!$department){
            return false;
        }
        $tempCourse =clone $course;
        $courseRule = CourseRule::find($course->course_rule_id);
        $tempCourseRule = clone $courseRule;
        $tempCourse->course_rule = $tempCourseRule;

        $course->course_code = $courseData['course_code'];
        $course->name = $courseData['course_name'];
        $course->department_id = $department->id;

        $courseRule->term_work = $courseData['term_work'];
        $courseRule->exam_work = $courseData['exam_work'];
        $courseRule->instructor = $courseData['instructor'];
        $courseRule->total = $courseData['total'];
        $course->save();
        $courseRule->save();
        $course->course_rule = $courseRule;
        $activity = activity()->causedBy(auth()->user())->performedOn($course)->
            withProperties(['old' => $tempCourse, 'new' => $course])->event('EDIT_COURSE')
            ->log('Edit course with id: '.$course->id.'' . ' and name: ' . $course->name . '');
            $activity->log_name = 'COURSE';
            $activity->save();
        return $course;
    }
    public function getCoursesInSemesterMerge(){
        $Allcourses = Course::all();
        $departments = Department::all();
        // get the leatest semester
        $semester = Semester::latest()->first();
        // get the courses id from course semester table by semester id
        $courses_id = CourseSemester::where('semester_id',$semester->id)->get('course_id');
        $coursesInSemester = [];
        foreach ($courses_id as $course_id){
            $coursesInSemester[] = Course::find($course_id->course_id);
        }
        $coursesNotInSemester = [];
        foreach ($Allcourses as $course){
            if(!in_array($course, $coursesInSemester)){
                $coursesNotInSemester[] = $course;
            }
        }
        return [
            'courses' => $coursesNotInSemester,
            'departments' => $departments,
            'coursesInSemester' => $coursesInSemester,
            'newestSemester' => $semester
        ];
    }
    public function importCourses($courses)
    {
        $courseData = Excel::toArray([], $courses)[0];
        $courseData = array_slice($courseData, 1);
        $isExist = false;
        $Allcourses = [];
        foreach ($courseData as $course) {
            // check if the course_code is not empty
            $c = Course::where('course_code', $course[0])->first();
                // check if department_id is exist
                $department_id = Department::where('id', $course[2])->first();
                if (!$c && !empty($course[0]) && !empty($course[1]) && !empty($course[2]) && $department_id) {
                    $isExist = true;
                    $c = new Course();
                    $c->course_code = $course[0];
                    $c->name = $course[1] ?? '';
                    $c->department_id = $course[2] ?? null;
                    $c->course_rule_id = CourseRule::factory()->create()->id;
                    $c->save();
                    $Allcourses[] = $c;
                }
    }

        return $Allcourses;
    }
    public function deleteCourse($course_id){
        $course = Course::find($course_id);
        if(!$course){
            return false;
        }
        $course->delete();
        return true;
    }
    public function calcGrade($grade)
    {
        if ($grade >= 90)
            return 'A+';
        elseif ($grade >= 85)
            return 'A';
        elseif ($grade >= 80)
            return 'B+';
        elseif ($grade >= 75)
            return 'B';
        elseif ($grade >= 70)
            return 'C+';
        elseif ($grade >= 65)
            return 'C';
        elseif ($grade >= 60)
            return 'D+';
        elseif ($grade >= 50)
            return 'D';
        else
            return 'F';
    }
    public function studentCourses($student_id){
    // get all course semester enrollments from table CourseSemesterEnrollment table by student id
    $enrollments = CourseSemesterEnrollment::where('student_id', $student_id)->get();
    $student = Student::where('id', $student_id)->first();
    $courses = [];
    foreach ($enrollments as $enrollment){
        $course_semester = CourseSemester::where('id', $enrollment->course_semester_id)->first();
        $course = Course::find($course_semester->course_id);
        $semester = Semester::find($course_semester->semester_id);
        $total = $enrollment->exam_work + $enrollment->term_work;
        $grade = $this->calcGrade($total);
        $courses[] = [
        'course_name' => $course->name,
        'semster_id' => $semester->id,
        'semester_year' => $semester->year,
        'semester_term' => $semester->term,
        'term_work' => $enrollment->term_work,
        'exam_work' => $enrollment->exam_work,
        'total_work' => $total,
        'grade' => $grade,
        ];
    }
    return [
      'student_name' => $student->name,
        'courses' => $courses
    ];
    }
}

