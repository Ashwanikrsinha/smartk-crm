<?php

namespace App\Http\Requests\Tally;

use Illuminate\Foundation\Http\FormRequest;

class SaleStoreRequest extends FormRequest
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
        'Sales' => 'required|array',
        'Sales.*.VOUCHERNUMBER' => 'sometimes|nullable|max:150',
        'Sales.*.DATE' => 'required|max:150',
        'Sales.*.VCHTYPE' => 'required|max:150',
        'Sales.*.GUID' => 'required|max:150',
        'Sales.*.PARTYNAME' => 'sometimes|nullable||max:150',
        'Sales.*.PARTYLEDGERNAME' => 'required|max:150',
        'Sales.*.ENTEREDBY' => 'sometimes|required|max:150',
        'Sales.*.BASICBUYERADDRESS.LIST' => 'sometimes|nullable|array',
        'Sales.*.ALLINVENTORYENTRIES.LIST' => 'sometimes|nullable|array',
        'Sales.*.LEDGERENTRIES.LIST' => 'sometimes|nullable|array',
        ];
    }
}
