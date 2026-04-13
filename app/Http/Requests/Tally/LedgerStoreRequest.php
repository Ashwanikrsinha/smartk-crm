<?php

namespace App\Http\Requests\Tally;

use Illuminate\Foundation\Http\FormRequest;

class LedgerStoreRequest extends FormRequest
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
        'LedgerHeader' => 'required|array',
        'LedgerHeader.*.CompanyName' => 'required|max:150',
        'LedgerHeader.*.LedgerName' => 'required|max:150',
        'LedgerHeader.*.MASTERID' => 'required|integer',
        'LedgerHeader.*.LedgerGroup' => 'required|max:150',
        'LedgerHeader.*.LedgerId' => 'sometimes|nullable|max:150',
        'LedgerHeader.*.GSTRegType' => 'sometimes|nullable|max:150',
        'LedgerHeader.*.State' => 'sometimes|nullable|max:150',
        ];
    }
}
