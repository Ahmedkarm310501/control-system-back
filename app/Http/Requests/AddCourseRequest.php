<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCourseRequest extends FormRequest
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
            'course_code' => 'required|string|unique:courses,course_code',
            'course_name' => 'required|string|unique:courses,name',
            'dept_code' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'course_code.required' => 'Course code is required',
            'course_code.string' => 'Course code must be a string',
            'course_code.unique' => 'Course code already exists',
            'course_name.required' => 'Course name is required',
            'course_name.string' => 'Course name must be a string',
            'course_name.unique' => 'Course name already exists',
            'department_code.required' => 'Department code is required',
            'department_code.string' => 'Department code must be a string',
        ];
    }
}
