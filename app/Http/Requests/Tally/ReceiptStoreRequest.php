<?php

namespace App\Http\Requests\Tally;

use Illuminate\Foundation\Http\FormRequest;

class ReceiptStoreRequest extends FormRequest
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
        'Receipt' => 'required|array',
        'Receipt.*.VoucherNo' => 'required|max:150',
        'Receipt.*.ReferenceNo' => 'required|max:150',
        'Receipt.*.TypeofRefNo' => 'required|max:150',
        'Receipt.*.VoucherType' => 'required|max:150',
        'Receipt.*.VoucherMasterId' => 'required|max:150',
        'Receipt.*.VoucherAlterId' => 'required|max:150',
        'Receipt.*.TallyCompanyName' => 'required|max:150',
        'Receipt.*.Date' => 'required|max:150',
        'Receipt.*.Amount' => 'required|numeric',
        'Receipt.*.PaymentMode' => 'required|max:150',
        'Receipt.*.ChequeNumber' => 'required|max:150',
        'Receipt.*.ChequeDate' => 'required|nullable',
        'Receipt.*.BankName' => 'required|max:150',
        'Receipt.*.DebitLedger' => 'required|max:150',
        'Receipt.*.CreditLedger' => 'required|max:150',
        ];
    }
}
