<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddDepartementRequest extends FormRequest
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
            'dept_code' => 'required|string|unique:departments,dept_code',
            'name' => 'required|string|unique:departments,name',
        ];
    }
}
