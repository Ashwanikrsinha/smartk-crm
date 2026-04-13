<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Invoice;


class InvoiceRequest extends FormRequest
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
        $status = Invoice::status();
        $follow_types = Invoice::followTypes();

        return [
            'invoice_date' => 'required|date',
            'customer_id' => 'required|integer',
            'visit_id' => 'required|integer',
            'phone_number' => 'nullable|max:100',
            'address' => 'nullable|max:200',
            'status' => 'required|'.Rule::in($status),
            'follow_date' => 'nullable|date',
            'follow_type' => 'nullable|'.Rule::in($follow_types),
            'reason' => 'nullable|max:6000',
            'remarks' => 'nullable|max:6000',
            'terms' => 'nullable|max:6000',
            
            'products' =>  'required|array',
            'products.*' => 'required|distinct',

            'descriptions' =>  'nullable|array',
            'descriptions.*' => 'nullable|max:150',

            'units' =>  'required|array',
            'units.*' => 'required|integer',
            
            'quantities' => 'required|array',
            'quantities.*' => 'required|numeric',
            
            'rates' => 'required|array',
            'rates.*' => 'required|numeric',

            'amounts' => 'required|array',
            'amounts.*' => 'required|numeric',

            'amount' => 'required|numeric',
            
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|max:200',
        ];
    }
}
