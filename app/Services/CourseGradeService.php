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
use App\Exports\GradesExport;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Hash;


class CourseGradeService
{

    public function checkCourseAccess($courseId, $user, $termId)
    {
        $course = Course::find($courseId);
        if (!$course) {
            throw new \Exception('Course not found', 404);
        }
        // check if the user has access to the course
        $course_user = CourseUser::where('user_id', $user->id)
            ->where('course_id', $course->id)->first();
        if (!$course_user && $user->is_admin != 1) {
            throw new \Exception('You do not have access to this course', 403);
        }
        // check if the course has the semester
        $semester = Semester::find($termId);
        if (!$semester) {
            throw new \Exception('Semester not found', 404);
        }
        $course_semester = CourseSemester::where('course_id', $course->id)
            ->where('semester_id', $termId)->first();
        if (!$course_semester) {
            throw new \Exception('Course does not have this semester', 404);
        }
        return [$course, $course_user, $semester, $course_semester];
    }

    public function addActivity($logName, $logMessage, $event, $preformedOn, $old, $new)
    {
        $activity = activity()->causedBy(auth()->user())->performedOn($preformedOn)->
            withProperties(['old' => $old, 'new' => $new])->event($event)
            ->log($logMessage);
        $activity->log_name = $logName;
        $activity->save();
    }


    public function getCourseGrades($courseId, $termId)
    {
        [$course, $course_user, $semester, $course_semester] =
            $this->checkCourseAccess($courseId, auth()->user(), $termId);
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

        if ($course_semester_enrollment->isEmpty()) {
            throw new \Exception('Course has no students enrolled', 404);
        }

        return $course_semester_enrollment;
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

    public function addStudentToCourse($data, $user)
    {

        [$course, $course_user, $semester, $course_semester] =
            $this->checkCourseAccess($data['course_id'], auth()->user(), $data['semester_id']);
        $student = Student::find($data['student_id']);
        if (!$student) {
            $student = Student::create([
                'name' => $data['student_name'],
                'id' => $data['student_id'],
            ]);
        }
        $course_semester_enrollment = CourseSemesterEnrollment::firstOrCreate([
            'course_semester_id' => $course_semester->id,
            'student_id' => $student->id,
        ]);
        if ($course_semester_enrollment) {
            $logMessage = 'Added student with id: ' . $student->id . ' and name: ' . $student->name . ' to : ' . $course->name;
            $this->addActivity('COURSE_NAME', $logMessage, 'ADD_STUDENT_TO_COURSE', $course_semester_enrollment, null, $course_semester_enrollment);
            return $course_semester_enrollment;
        }
        throw new \Exception('Error adding student to course', 500);
    }

    public function addStudentsToCourseExcel($data, $user)
    {

        [$course, $course_user, $semester, $course_semester] =
            $this->checkCourseAccess($data['course_id'], auth()->user(), $data['semester_id']);
        $students = Excel::toArray([], $data['students'])[0];
        $students = array_slice($students, 1);
        $numOfMissingFields = 0;
        $studentsRes = [];
        foreach ($students as $student) {
            if (!isset($student[0]) || !isset($student[1])) {
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

        $logMessage = 'Added student names file to course ' . $course->name;
        $this->addActivity(
            'COURSE_NAMES',
            $logMessage,
            'ADD_STUDENTS_TO_COURSE_EXCEL',
            $course_semester,
            ['course_name' => $course->name, 'old_file' => $course_semester->stud_names],
            ['course_name' => $course->name, 'new_file' => $filePath]
        );

        $course_semester->stud_names = $filePath;
        $course_semester->save();
        if (count($studentsRes) > 0) {
            return [
                'students' => $studentsRes,
                'numOfMissingFields' => $numOfMissingFields,
                'filePath' => $filePath,
            ];
        }
        throw new \Exception('Error adding student to course', 500);

    }

    public function deleteStudentFromCourse($data)
    {
        [$course, $course_user, $semester, $course_semester] =
            $this->checkCourseAccess($data['course_id'], auth()->user(), $data['semester_id']);

        $student = Student::find($data['student_id']);
        if (!$student) {
            throw new \Exception('Student not found', 404);
        }

        $course_semester_enrollment = CourseSemesterEnrollment::with('student:id,name')->
            where('course_semester_id', $course_semester->id)
            ->where('student_id', $student->id);
        $temp = clone $course_semester_enrollment->first();
        $course_semester_enrollment->delete();
        if ($course_semester_enrollment) {
            $logMessage = 'Deleted student with id: ' . $student->id . ' and name: ' . $student->name . ' from : ' . $course->name;
            $this->addActivity('COURSE_NAME', $logMessage, 'DELETE_STUDENT_FROM_COURSE', $course_semester, $temp, null);
            return $course_semester_enrollment;
        }
        throw new \Exception('Error deleting student from course', 500);

    }

    public function deleteAllStudentsFromCourse($data)
    {
        if (!Hash::check($data['user_password'], auth()->user()->password)) {
            throw new \Exception('Wrong password', 403);
        }
        [$course, $course_user, $semester, $course_semester] =
            $this->checkCourseAccess($data['course_id'], auth()->user(), $data['semester_id']);
        $course_semester_enrollment = CourseSemesterEnrollment::with('student:name,id')->
            where('course_semester_id', $course_semester->id);

        $temp = clone $course_semester_enrollment;

        $Students = [];
        foreach ($course_semester_enrollment->get() as $enrollment) {
            $Student = [];
            $Student[] = $enrollment->student->id;
            $Student[] = $enrollment->student->name;
            $Students[] = $Student;
        }

        $filename = uniqid() . '.' . 'xlsx';
        Excel::store(new CourseNamesExport($Students), $filename, 'public');
        $filePath = Storage::url($filename);




        $course_semester_enrollment->delete();
        if ($course_semester_enrollment) {
            $logMessage = 'Deleted all students from course ' . $course->name;
            $old = ['course_name' => $course->name, 'old_file' => $filePath];
            $this->addActivity('COURSE_NAME', $logMessage, 'DELETE_ALL_STUDENTS_FROM_COURSE', $course_semester, $old, null);
            return $course_semester_enrollment;
        }
        $course_semester->stud_names = null;
        $course_semester->stud_grades = null;
        $course_semester->save();

        throw new \Exception('Error deleting student from course', 500);
    }

    public function addOneStudentGrade($data)
    {

        [$course, $course_user, $semester, $course_semester] =
            $this->checkCourseAccess($data['course_id'], auth()->user(), $data['semester_id']);
        $student = Student::find($data['student_id']);
        if (!$student) {
            throw new \Exception('Student not found', 404);
        }
        $course_semester_enrollment = CourseSemesterEnrollment::
            where('course_semester_id', $course_semester->id)
            ->where('student_id', $student->id)
            ->first();
        $temp = clone $course_semester_enrollment;
        // dd($temp);
        if (!$course_semester_enrollment) {
            throw new \Exception('Student not enrolled in this course', 404);
        }
        // check if stud term work between 0 and 40 and exam work between 0 and 60
        if ($data['term_work'] < 0 || $data['term_work'] > 40 || $data['exam_work'] < 0 || $data['exam_work'] > 60) {
            throw new \Exception('Term work must be between 0 and 40 and exam work must be between 0 and 60', 400);
        }
        $course_semester_enrollment = CourseSemesterEnrollment::
            where('course_semester_id', $course_semester->id)
            ->where('student_id', $student->id)
            ->update([
                'term_work' => $data['term_work'],
                'exam_work' => $data['exam_work'],
            ]);
        // $temp_old = clone $temp::with('student:name,id')->first();
        $temp_old = clone $temp;
        $temp->term_work = $data['term_work'];
        $temp->exam_work = $data['exam_work'];

        $temp->name = $student->name;
        $temp_old->name = $student->name;
        if ($course_semester_enrollment) {

            $logMessage = 'Updated student grade for student with id: ' . $student->id . ' and name: ' . $student->name . ' in course: ' . $course->name;
            $this->addActivity('COURSE_NAME', $logMessage, 'UPDATE_STUDENT_GRADE', $temp, $temp_old, $temp);
            return $course_semester_enrollment;
        }
        throw new \Exception('Error updating student grade', 500);

    }


    public function deleteCourseGrades($data)
    {
        if (!Hash::check($data['user_password'], auth()->user()->password)) {
            throw new \Exception('Wrong password', 403);
        }
        [$course, $course_user, $semester, $course_semester] =
            $this->checkCourseAccess($data['course_id'], auth()->user(), $data['semester_id']);
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

        foreach ($course_semester_enrollment as $enrollment) {
            $courseGrade = [];
            $courseGrade[] = $enrollment->student->id;
            $courseGrade[] = $enrollment->student->name;
            $courseGrade[] = $enrollment->term_work;
            $courseGrade[] = $enrollment->exam_work;
            $courseGrade[] = $enrollment->total_grade;
            $courseGrade[] = $enrollment->grade;
            $courseGrades[] = $courseGrade;
        }

        $filename = uniqid() . '.' . 'xlsx';
        Excel::store(new GradesExport($courseGrades), $filename, 'public');
        $filePath = Storage::url($filename);

        $course_semester_enrollment = CourseSemesterEnrollment::with('student:name,id')
            ->where('course_semester_id', $course_semester->id)
            ->update([
                'term_work' => null,
                'exam_work' => null,
            ]);
        if ($course_semester_enrollment) {
            $logMessage = 'Deleted course grades for course: ' . $course->name;
            $old = ['course_name' => $course->name, 'old_file' => $filePath];
            $this->addActivity('COURSE_GRADES', $logMessage, 'DELETE_COURSE_GRADES', $course_semester, $old, null);
            return $course_semester_enrollment;
        }
        throw new \Exception('Error deleting course grades', 500);

    }




    public function exportCourseGrades($data)
    {
        [$course, $course_user, $semester, $course_semester] =
            $this->checkCourseAccess($data['course_id'], auth()->user(), $data['semester_id']);
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
        foreach ($course_semester_enrollment as $enrollment) {
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

        if (count($courseGrades) > 0) {
            return $courseGrades;
        }
        throw new \Exception('Error exporting course grades', 500);

    }
    public function insertGrade($courseData)
    {
        $course_semester_id = CourseSemester::where('course_id', $courseData['course_id'])
            ->where('semester_id', $courseData['semester_id'])->first()->id;

        $course_enrollment = CourseSemesterEnrollment::where('course_semester_id', $course_semester_id)
            ->where('student_id', $courseData['student_id'])->update([
                    'exam_work' => $courseData['exam_work'],
                ]);

        if ($course_enrollment) {
            return true;
        }

    }

    public function addStudentsGradesExcel($data)
    {
        [$course, $course_user, $semester, $course_semester] =
            $this->checkCourseAccess($data['course_id'], auth()->user(), $data['semester_id']);
        $students = Excel::toArray([], $data['students'])[0];
        $students = array_slice($students, 1);
        $wrongFormat = [];
        $index = 1;
        foreach ($students as $student) {
            if (!isset($student[0]) || !isset($student[1]) || !isset($student[2])) {
                $wrongFormat[] = $index;
                $index++;
                continue;
            }
            // check if stud term work between 0 and 40 and exam work between 0 and 60
            if ($student[1] < 0 || $student[1] > 40 || $student[2] < 0 || $student[2] > 60) {
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
        $courseGrades = [];
        foreach ($course_semester_enrollment as $enrollment) {
            $courseGrade = [];
            $courseGrade[] = $enrollment->student->id;
            $courseGrade[] = $enrollment->student->name;
            $courseGrade[] = $enrollment->term_work;
            $courseGrade[] = $enrollment->exam_work;
            $courseGrade[] = $enrollment->total_grade;
            $courseGrade[] = $enrollment->grade;
            $courseGrades[] = $courseGrade;
        }

        $filename = uniqid() . '.' . 'xlsx';
        Excel::store(new GradesExport($courseGrades), $filename, 'public');
        $filePath = Storage::url($filename);


        $studWithNoGrade = false;
        foreach ($course_semester_enrollment as $enrollment) {
            if ($enrollment->term_work === null || $enrollment->exam_work === null) {
                $studWithNoGrade = true;
            }
        }
        // print($course_semester_enrollment);
        if ($course_semester_enrollment) {
            $logMessage = 'Added course grades file for course: ' . $course->name;
            $old = ['course_name' => $course->name, 'old_file' => $course_semester->stud_grades];
            $new = ['course_name' => $course->name, 'new_file' => $filePath];
            $this->addActivity('COURSE_GRADES', $logMessage, 'ADD_COURSE_GRADES', $course_semester, $old, $new);

            $course_semester->stud_grades = $filePath;
            $course_semester->save();
            return [
                'course_semester_enrollment' => $course_semester_enrollment,
                'studWithNoGrade' => $studWithNoGrade,
                'wrongFormat' => $wrongFormat,
            ];
        }
        throw new \Exception('Error adding student to course', 500);

    }
    /// function to add student term work with excel file
    public function addStudentTermWork($data)
    {
        [$course, $course_user, $semester, $course_semester] =
            $this->checkCourseAccess($data['course_id'], auth()->user(), $data['semester_id']);
        $students = Excel::toArray([], $data['students'])[0];
        $students = array_slice($students, 1);
        $wrongFormat = [];
        $index = 1;
        foreach ($students as $student) {
            if (!isset($student[0]) || !isset($student[1])) {
                $wrongFormat[] = $index;
                $index++;
                continue;
            }
            // check if stud term work between 0 and 40
            if ($student[1] < 0 || $student[1] > 40) {
                $wrongFormat[] = $index;
                $index++;
                continue;
            }
            $course_enrollment = CourseSemesterEnrollment::
                where('course_semester_id', $course_semester->id)
                ->where('student_id', $student[0])
                ->update([
                    'term_work' => $student[1],
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
        $courseGrades = [];
        foreach ($course_semester_enrollment as $enrollment) {
            $courseGrade = [];
            $courseGrade[] = $enrollment->student->id;
            $courseGrade[] = $enrollment->student->name;
            $courseGrade[] = $enrollment->term_work;
            $courseGrade[] = $enrollment->exam_work;
            $courseGrade[] = $enrollment->total_grade;
            $courseGrade[] = $enrollment->grade;
            $courseGrades[] = $courseGrade;

        }
        $filename = uniqid() . '.' . 'xlsx';
        Excel::store(new GradesExport($courseGrades), $filename, 'public');
        $filePath = Storage::url($filename);
        $studWithNoGrade = false;
        foreach ($course_semester_enrollment as $enrollment) {
            if ($enrollment->term_work === null || $enrollment->exam_work === null) {
                $studWithNoGrade = true;
            }
        }
        if ($course_semester_enrollment) {
            // $logMessage = 'Added course term work file for course: ' . $course->name;
            // $old = ['course_name' => $course->name, 'old_file' => $course_semester->stud_term_work];
            // $new = ['course_name' => $course->name, 'new_file' => $filePath];
            // $this->addActivity('COURSE_TERM_WORK', $logMessage, 'ADD_COURSE_TERM_WORK', $course_semester, $old, $new);

            // $course_semester->stud_term_work = $filePath;
            // $course_semester->save();
            return [
                'course_semester_enrollment' => $course_semester_enrollment,
                'studWithNoGrade' => $studWithNoGrade,
                'wrongFormat' => $wrongFormat,
            ];
        }
        throw new \Exception('Error adding student term work to course', 500);


    }

}