<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertGradeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => 'required|integer|exists:students,id',
            'course_id' => 'required|integer|exists:courses,id',
            'semester_id' => 'required|integer|exists:semesters,id',
            'exam_work' => 'required|numeric|min:0|max:60',
        ];
    }
    public function messages(){
        return [
          'student_id.required' => 'Student ID is required',
          'student_id.integer' => 'Student ID must be an integer',
          'student_id.exists' => 'Student ID does not exist',
            'course_id.required' => 'Course ID is required',
            'course_id.integer' => 'Course ID must be an integer',
            'course_id.exists' => 'Course ID does not exist',
          'semester_id.required' => 'Semester ID is required',
          'semester_id.integer' => 'Semester ID must be an integer',
          'semester_id.exists' => 'Semester ID does not exist',
            'exam_work.required' => 'Exam work is required',
            'exam_work.numeric' => 'Exam work must be a number',
            'exam_work.min' => 'Exam work must be at least 0',
        ];
    }
}
