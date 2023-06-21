<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RafaaGradesRequest extends FormRequest
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
            'number_of_grades' => 'required|integer',
            'AllOrFailed' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'couse_id.required' => 'Course id is required',
            'couse_id.integer' => 'Course id must be an integer',
            'number_of_grades.required' => 'Number of grades is required',
            'number_of_grades.integer' => 'Number of grades must be an integer',
        ];
    }
}
