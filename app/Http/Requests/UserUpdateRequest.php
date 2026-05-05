<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
            'username' => 'bail|required|'.Rule::unique('users', 'username')->ignore($this->user),
            'emp_code' => 'bail|required|'.Rule::unique('users', 'emp_code')->ignore($this->user),
            'email' => 'bail|required|max:100|'.Rule::unique('users', 'email')->ignore($this->user),
            'role_id' => 'required|integer',
            'department_id' => 'required|integer',
            'reportive_id' => 'nullable|integer',
            'is_disable' => 'nullable|boolean',
            'password' => 'nullable|confirmed|min:5',
        ];
    }
}
