<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransportRequest extends FormRequest
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
            'name' => 'required|max:150|'.Rule::unique('transports', 'name')->ignore($this->transport),
            'address' => 'nullable|max:150',
            'phone_number' => 'nullable|max:150',
            'gst_number' => 'nullable|max:150',
        ];
    }
}