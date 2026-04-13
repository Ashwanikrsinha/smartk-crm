<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
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
            'name' => 'required|max:100|'.Rule::unique('customers', 'name')->ignore($this->customer),
            'segment_id' => 'nullable|integer',
            'email' => 'nullable|max:100',
            'city' => 'nullable|max:50',
            'state' => 'nullable|max:50',
            'address' => 'nullable|max:150',
            'phone_number' => 'nullable|max:150',
            'gst_number' => 'nullable|max:150',
            'description' => 'nullable|max:6000',
            
            'customer_types' => 'nullable|array',
            'person_name' => 'nullable|array',
            'birth_date' => 'nullable|array',
            'marriage_date' => 'nullable|array',
            'contact_number' => 'nullable|array',
            'designation_id' => 'nullable|array'
        ];
    }
}
