<?php

namespace App\Models\Tally;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public $timestamps = false;

    public static function createItems($request)
    {
        
        foreach ($request->StockItemHeader as $i => $data) {
        
             Item::create([
                'company_name' => $data['CompanyName'],
                'name' => $data['StockItemName'],
                'unit' => $data['StockItemUnit'],
                'category' => $data['StockItemCategory'],
                'group' => $data['StockItemGroup'],
                'hsn_code' => $data['StockItemHSNCode'],
                'tax_rate' => $data['StockItemTaxRate'],
                'item_alias' => $data['StockItemAlias'] ?? null,
                'part_number' => $data['StockItemPartNumber'] ?? null,
                'master_id' => $data['MASTERID'],
                'item_id' => $data['StockItemId'] ?? null
             ]);
 
         }
         
    }
}
