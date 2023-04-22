<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddStudGradeRequest extends FormRequest
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
            'term_work' => 'required|numeric',
            'exam_work' => 'required|numeric',
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
            'term_work.required' => 'Term work is required',
            'term_work.numeric' => 'Term work must be a number',
            'exam_work.required' => 'Final exam is required',
            'exam_work.numeric' => 'Final exam must be a number',
        ];
    }
}
