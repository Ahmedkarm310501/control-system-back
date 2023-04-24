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
            'national_id' => 'required|numeric',
            'is_admin' => 'required|boolean',
            'is_active' => 'required|boolean',
            'password' => 'required',
        ];
    }
    // public function messages(){
    //     return [
    //         'id.required' => 'ID is required',
    //         'id.numeric' => 'ID must be numeric',
    //         'name.required' => 'Name is required',
    //         'email.required' => 'Email is required',
    //         'national_id.required' => 'National ID is required',
    //         'is_admin.required' => 'Admin status is required',
    //         'is_admin.boolean' => 'Admin status must be boolean',
    //         'is_active.boolean' => 'Active status must be boolean',
    //         'is_active.required' => 'Active status is required',
    //         'password.required' => 'Password is required',
    //     ];
    // }
}
