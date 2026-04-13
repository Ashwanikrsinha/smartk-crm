<?php

namespace App\Http\Requests\Tally;

use Illuminate\Foundation\Http\FormRequest;

class PaymentStoreRequest extends FormRequest
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
        'Payment' => 'required|array',
        'Payment.*.VoucherNo' => 'required|max:150',
        'Payment.*.ReferenceNo' => 'required|max:150',
        'Payment.*.TypeofRefNo' => 'required|max:150',
        'Payment.*.VoucherType' => 'required|max:150',
        'Payment.*.VoucherMasterId' => 'required|max:150',
        'Payment.*.VoucherAlterId' => 'required|max:150',
        'Payment.*.TallyCompanyName' => 'required|max:150',
        'Payment.*.Date' => 'required|max:150',
        'Payment.*.Amount' => 'required|numeric',
        'Payment.*.PaymentMode' => 'required|max:150',
        'Payment.*.ChequeNumber' => 'required|max:150',
        'Payment.*.ChequeDate' => 'required|nullable',
        'Payment.*.BankName' => 'required|max:150',
        'Payment.*.DebitLedger' => 'required|max:150',
        'Payment.*.CreditLedger' => 'required|max:150',
        ];
    }
}
