<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompareCoursesSemestersRequest extends FormRequest
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
            'course_id_one' => 'required|integer|exists:courses,id',
            'course_id_two' => 'required|integer|exists:courses,id',
            'semester_id_one' => 'required|integer|exists:semesters,id',
            'semester_id_two' => 'required|integer|exists:semesters,id',
        ];
    }
    public function messages(): array
    {
        return [
            'course_id_one.required' => 'course_id_one is required',
            'course_id_one.integer' => 'course_id_one must be an integer',
            'course_id_one.exists' => 'course_id_one does not exist',
            'course_id_two.required' => 'course_id_two is required',
            'course_id_two.integer' => 'course_id_two must be an integer',
            'course_id_two.exists' => 'course_id_two does not exist',
            'semester_id_one.required' => 'semester_id_one is required',
            'semester_id_one.integer' => 'semester_id_one must be an integer',
            'semester_id_one.exists' => 'semester_id_one does not exist',
            'semester_id_two.required' => 'semester_id_two is required',
            'semester_id_two.integer' => 'semester_id_two must be an integer',
            'semester_id_two.exists' => 'semester_id_two does not exist',
        ];
    }
}
