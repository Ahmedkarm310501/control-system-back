<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompareTwoSemesterRequest extends FormRequest
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
            'course_id' => 'required|integer',
            'semester_id_one' => 'required|integer',
            'semester_id_two' => 'required|integer',
        ];
    }
    public function messages(): array
    {
        return [
            'course_id.required' => 'Course id is required',
            'course_id.integer' => 'Course id must be an integer',
            'semester_id_one.required' => 'Semester id one is required',
            'semester_id_one.integer' => 'Semester id one must be an integer',
            'semester_id_two.required' => 'Semester id two is required',
            'semester_id_two.integer' => 'Semester id two must be an integer',
        ];
    }
}
