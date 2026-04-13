<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'bail|required|max:100|'.Rule::unique('employees', 'name')->ignore($this->employee),
            'department_id' => 'required|integer',
            'email' => 'required|email|'.Rule::unique('employees', 'email')->ignore($this->employee),
            'phone_number' => 'nullable|max:100',
            'birth_date' => 'nullable|date',
            'marital_status' => 'nullable|string|max:50',
            'marriage_date' => 'nullable|date',
            'state' => 'nullable|max:150',
            'city' => 'nullable|max:150',
            'address' => 'nullable|max:150',
            'qualification' => 'nullable|max:6000',
            'salary' => 'nullable|max:50',
            'hr_allowance' => 'nullable|max:50',
            'convey_allowance' => 'nullable|max:50',
            'spi_allowance' => 'nullable|max:50',
            'joining_date' => 'nullable|date',
            'resign_date' => 'nullable|date',
            'resign_reason' => 'nullable|max:150',
            'can_login' => 'nullable|boolean',
        
            'username' => 'sometimes|required||unique:users',
            'email' => 'sometimes|required|email|unique:users',
            'role_id' => 'sometimes|required|integer',
            'department_id' => 'sometimes|required|integer',
            'reportive_id' => 'nullable|integer',
            'is_disable' => 'nullable|boolean',
            'password' => 'sometimes|required|confirmed|min:5',
        ];
    }
}
