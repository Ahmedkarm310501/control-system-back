<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportCoursesRequest extends FormRequest
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
            // courses excel file
            'courses' => 'required|file|mimes:xlsx,xls,csv',
        ];
    }
    public function messages(): array
    {
        return [
            'courses.required' => 'Please upload a file',
            'courses.mimes' => 'Only xlsx, xls and csv files are allowed',
        ];
    }
}
