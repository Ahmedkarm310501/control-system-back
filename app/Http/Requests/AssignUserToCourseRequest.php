<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignUserToCourseRequest extends FormRequest
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
            'user_id' => 'required|integer',
            'course_id' => 'required|integer',
        ];
    }
    public function messages(): array
    {
        return [
            'user_id.required' => 'User Id is required',
            'user_id.integer' => 'User Id must be an integer',
            'course_id.required' => 'Course Id is required',
            'course_id.integer' => 'Course Id must be an integer',
        ];
    }
}
