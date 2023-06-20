<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NumberStudentsRequest extends FormRequest
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
            'course_id' => 'required|exists:courses,id',
            
        ];
    }
    public function messages(): array
    {
        return [
            'course_id.required' => 'Course id is required',
            'course_id.exists' => 'Course id does not exist',
        ];
    }
}
