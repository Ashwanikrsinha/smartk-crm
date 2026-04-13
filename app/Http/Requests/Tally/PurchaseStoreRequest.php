<?php

namespace App\Http\Requests\Tally;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseStoreRequest extends FormRequest
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
            'Purchases' => 'required|array',
            'Purchases.*.VOUCHERNUMBER' => 'sometimes|nullable|max:150',
            'Purchases.*.DATE' => 'required|max:150',
            'Purchases.*.VCHTYPE' => 'required|max:150',
            'Purchases.*.GUID' => 'required|max:150',
            'Purchases.*.PARTYNAME' => 'sometimes|nullable||max:150',
            'Purchases.*.PARTYLEDGERNAME' => 'required|max:150',
            'Purchases.*.ENTEREDBY' => 'sometimes|required|max:150',
            'Purchases.*.BASICBUYERADDRESS.LIST' => 'sometimes|nullable|array',
            'Purchases.*.ALLINVENTORYENTRIES.LIST' => 'sometimes|nullable|array',
            'Purchases.*.LEDGERENTRIES.LIST' => 'sometimes|nullable|array',
        ];
    }
}
