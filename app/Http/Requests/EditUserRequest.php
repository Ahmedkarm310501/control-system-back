<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
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
            'id' => 'required|numeric',
            'name' => 'required',
            'email' => 'required|email',
            'national_id' => 'nullable|string',
            'is_admin' => 'required|boolean',
            'is_active' => 'required|boolean',
        ];
    }
    
    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'Id is required',
            'id.numeric' => 'Id must be a number',
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email',
            'national_id.string' => 'National id must be a string',
            'is_admin.required' => 'Is admin is required',
            'is_admin.boolean' => 'Is admin must be a boolean',
            'is_active.required' => 'Is active is required',
            'is_active.boolean' => 'Is active must be a boolean',
        ];
    }
}
