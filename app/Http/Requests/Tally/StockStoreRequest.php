<?php

namespace App\Http\Requests\Tally;

use Illuminate\Foundation\Http\FormRequest;

class StockStoreRequest extends FormRequest
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
        'GodownItemStock' => 'required|array',
        'GodownItemStock.*.GodownName' => 'required|max:150',
        'GodownItemStock.*.ItemName' => 'required|max:150',
        'GodownItemStock.*.StockOpening' => 'required|max:150',
        'GodownItemStock.*.StockClosing' => 'required|max:150',
        'GodownItemStock.*.StockInward' => 'required|max:150',
        'GodownItemStock.*.StockOutward' => 'required|max:150',
        ];
    }
}
