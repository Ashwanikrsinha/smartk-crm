<?php

namespace App\Models\Tally;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'voucher_date' => 'date:d M, Y',
        'buyer_address_list' => 'array',
        'inventories_list' => 'array',
        'ledger_entries_list' => 'array'
    ];
    
    protected $dates = [
        'voucher_date'
    ];
    

    public static function createPurchases($request)
    {
        
        foreach ($request->Purchases as $i => $data) {
        
            Purchase::create([
               'voucher_number' => $data['VOUCHERNUMBER'] ?? null,
               'voucher_date' => date('Y-m-d', strtotime($data['DATE'])),
               'voucher_type' => $data['VCHTYPE'],
               'gu_id' => $data['GUID'],
               'party_name' => $data['PARTYNAME'] ?? null,
               'ledger_name' => $data['PARTYLEDGERNAME'],
               'entered_by' => $data['ENTEREDBY'] ?? null,
               'buyer_address_list' => $data['BASICBUYERADDRESS.LIST'] ?? null,
               'inventories_list' => $data['ALLINVENTORYENTRIES.LIST'] ?? null,
               'ledger_entries_list' => $data['LEDGERENTRIES.LIST'] ?? null
            ]);

        }
        
    }
}
