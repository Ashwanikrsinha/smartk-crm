<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
            'username' => 'bail|required|unique:users',
            'email' => 'required|email|unique:users',
            'role_id' => 'required|integer',
            'department_id' => 'required|integer',
            'reportive_id' => 'nullable|integer',
            'is_disable' => 'nullable|boolean',
            'password' => 'bail|required|confirmed|min:5',
        ];
    }
}
