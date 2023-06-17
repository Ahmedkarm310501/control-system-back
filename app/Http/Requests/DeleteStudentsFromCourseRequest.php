<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteStudentsFromCourseRequest extends FormRequest
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
            'semester_id' => 'required|numeric',
        
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
            'student_id.required' => 'Student ID is required',
            'student_id.numeric' => 'Student ID must be numeric',
        ];
    }
}
