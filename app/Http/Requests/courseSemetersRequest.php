<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class courseSemetersRequest extends FormRequest
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
            'course_id' => 'required|integer|exists:courses,id',
        ];
    }
    public function messages(): array
    {
        return [
            'course_id.required' => 'course id is required',
            'course_id.integer' => 'course id must be an integer',
            'course_id.exists' => 'course id does not exist',
        ];
    }
}
