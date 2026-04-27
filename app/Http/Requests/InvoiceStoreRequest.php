<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Policy handles authorization
    }

    public function rules(): array
    {
        return [
            // PO header
            'customer_id'       => 'required|exists:customers,id',
            'invoice_date'      => 'required|date',
            'delivery_due_date' => 'nullable|date|after_or_equal:invoice_date',
            'visit_id'          => 'nullable|exists:visits,id',
            'phone_number'      => 'nullable|string|max:15',
            'address'           => 'nullable|string|max:500',
            'remarks'           => 'nullable|string|max:1000',
            'terms'             => 'nullable|string|max:2000',
            'total_po_amount'   => 'required|numeric|min:0.01',
            'action'            => 'required|in:draft,submit',

            // Line items
            'products'          => 'required|array|min:1',
            'products.*'        => 'required|exists:items,id',
            'quantities.*'      => 'required|numeric|min:1',
            'rates.*'           => 'required|numeric|min:0',
            'amounts.*'         => 'required|numeric|min:0',
            'discounts.*'       => 'nullable|numeric|min:0|max:100',

            // PDCs (optional)
            'pdc_dates'         => 'nullable|array',
            'pdc_dates.*'       => 'nullable|date',
            'pdc_cheque_numbers.*' => 'nullable|string|max:50',
            'pdc_bank_names.*'  => 'nullable|string|max:100',
            'pdc_amounts.*'     => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Please select a school.',
            'products.required'    => 'At least one order item is required.',
            'products.min'         => 'At least one order item is required.',
            'total_po_amount.min'  => 'PO amount must be greater than zero.',
        ];
    }
}
