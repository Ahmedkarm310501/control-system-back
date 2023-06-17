<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditCourseSettingsRequest extends FormRequest
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
            'course_id' => 'required|numeric',
            'course_name' => 'required|string',
            'course_code' => 'required|string',
            'dept_code' => 'required|string',
            'term_work' => 'required|numeric',
            'exam_work' => 'required|numeric',
            'total' => 'required|numeric',
            'instructor' => 'required|string',
            'semester_id' => 'required|numeric'
        ];
       
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'course_id.required' => 'Course ID is required',
            'course_id.numeric' => 'Course ID must be numeric',
            'course_name.required' => 'Course name is required',
            'course_code.required' => 'Course code is required',
            'dept_code.required' => 'Department code is required',
            'term_work.required' => 'Term work is required',
            'term_work.numeric' => 'Term work must be numeric',
            'exam_work.required' => 'Exam work is required',
            'exam_work.numeric' => 'Exam work must be numeric',
            'total.required' => 'Total is required',
            'total.numeric' => 'Total must be numeric',
            'instructor.required' => 'Instructor is required',
            'semester_id.required' => 'Semester ID is required',
            'semester_id.numeric' => 'Semester ID must be numeric'
        ];
    }
}
