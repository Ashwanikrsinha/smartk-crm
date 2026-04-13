<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Quotation;

class QuotationRequest extends FormRequest
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
        
        $status = Quotation::status();
        $follow_types = Quotation::followTypes();

        return [
            'quotation_date' => 'required|date',
            'user_id' => 'required|integer',
            'customer_id' => 'required|integer',
            'status' => 'required|'.Rule::in($status),
            'follow_date' => 'nullable|date',
            'follow_type' => 'nullable|'.Rule::in($follow_types),
            'remarks' => 'nullable|max:6000',
            'reason' => 'nullable|max:6000',
            
            'products' =>  'sometimes|required|array',
            'products.*' => 'required|distinct',

            'descriptions' =>  'nullable|array',
            'descriptions.*' => 'nullable|max:150',

            'units' =>  'sometimes|required|array',
            'units.*' => 'required|integer',
            
            'quantities' => 'sometimes|required|array',
            'quantities.*' => 'required|numeric',
            
            'rates' => '  sometimes|required|array',
            'rates.*' => 'required|numeric',

            'attachments' => 'nullable|array',
        ];
    }
}
