<?php

namespace App\Models\Tally;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public $timestamps = false;

    public static function createStocks($request)
    {
        
        foreach ($request->GodownItemStock as $i => $data) {
        
            Stock::create([
               'godown_name' => $data['GodownName'],
               'item_name' => $data['ItemName'],
               'stock_opening' => $data['StockOpening'],
               'stock_closing' => $data['StockClosing'],
               'stock_inward' => $data['StockInward'],
               'stock_outward' => $data['StockOutward'],
            ]);

        }
        
    }
}
