<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteStudentFromCourseRequest extends FormRequest
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
            'course_id' => 'required|string',
            'student_id' => 'required|integer',
            'semester_id' => 'required|integer',
        ];
    }

    public function messages(){
        return [
            'course_id.required' => 'Course id is required',
            'course_id.string' => 'Course id must be a string',
            'student_id.required' => 'Student id is required',
            'student_id.integer' => 'Student id must be an integer',
            'semester_id.required' => 'Semester id is required',
            'semester_id.integer' => 'Semester id must be an integer',
        ];
    }
}
