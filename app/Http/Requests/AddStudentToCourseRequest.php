<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddStudentToCourseRequest extends FormRequest
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
            'student_id' => 'required|integer',
            'course_id' => 'required|integer',
            'semester_id' => 'required|integer',
            'student_name' => 'required|string',
        ];
    }

    public function messages(){
        return [
            'student_id.required' => 'Student id is required',
            'student_id.integer' => 'Student id must be an integer',
            'course_id.required' => 'Course id is required',
            'course_id.integer' => 'Course id must be an integer',
            'semester_id.required' => 'Semester id is required',
            'semester_id.integer' => 'Semester id must be an integer',
            'student_name.required' => 'Student student_name is required',
            'student_name.string' => 'Student student_name must be a string',
        ];
    }
}
