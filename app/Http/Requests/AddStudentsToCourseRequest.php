<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddStudentsToCourseRequest extends FormRequest
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
            // students excel file
            'students' => 'required|file|mimes:xlsx,xls,csv',
            'course_id' => 'required|integer',
            'semester_id' => 'required|integer',
        ];
    }

    public function messages(){
        return [
            'students.required' => 'Students file is required',
            'students.file' => 'Students file must be a file',
            'students.mimes' => 'Students file must be a file of type: xlsx, xls, csv',
            'course_id.required' => 'Course id is required',
            'course_id.integer' => 'Course id must be an integer',
            'semester_id.required' => 'Semester id is required',
            'semester_id.integer' => 'Semester id must be an integer',
        ];
    }
}
