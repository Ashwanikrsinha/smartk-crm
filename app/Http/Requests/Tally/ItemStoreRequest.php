<?php

namespace App\Http\Requests\Tally;

use Illuminate\Foundation\Http\FormRequest;

class ItemStoreRequest extends FormRequest
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
        'StockItemHeader' => 'required|array',
        'StockItemHeader.*.CompanyName' => 'required|max:150',
        'StockItemHeader.*.StockItemName' => 'required|max:150',
        'StockItemHeader.*.StockItemUnit' => 'required|max:150',
        'StockItemHeader.*.StockItemCategory' => 'required|max:150',
        'StockItemHeader.*.StockItemGroup' => 'required|max:150',
        'StockItemHeader.*.StockItemHSNCode' => 'required|max:150',
        'StockItemHeader.*.StockItemTaxRate' => 'required|max:150|numeric',
        'StockItemHeader.*.StockItemAlias' => 'sometimes|nullable|max:150',
        'StockItemHeader.*.StockItemId' => 'sometimes|nullable|max:150',
        'StockItemHeader.*.StockItemPartNumber' => 'sometimes|nullable|integer',
        'StockItemHeader.*.MASTERID' => 'required|integer',
        ];
    }
}
