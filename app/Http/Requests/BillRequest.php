<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Bill;

class BillRequest extends FormRequest
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
            'type' => 'required|'.Rule::in([ Bill::SALE, Bill::PURCHASE]),
            'bill_date' => 'required|date',
            'customer_id' => 'required|integer',
            'phone_number' => 'nullable|max:100',
            'gst_number' => 'nullable|max:100',
            'address' => 'required|max:200',
            'is_approved' => 'nullable|boolean',
            'transport_id' => 'nullable|integer',
            'vehicle_number' => 'nullable|max:150',
            'bilty_number' => 'nullable|max:150',
            'delivery_date' => 'nullable|date',
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
            'cgst_amount' => 'required|numeric',
            'sgst_amount' => 'required|numeric',
            'igst_amount' => 'required|numeric',
            'transport_charges' => 'required|numeric',
            'extra_charges' => 'required|numeric',
            'total_amount' => 'required|numeric',
            
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|max:200',
        ];
    }
}
