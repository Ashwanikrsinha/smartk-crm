<?php

namespace App\Models\Tally;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'buyer_address_list' => 'array',
        'inventories_list' => 'array',
        'ledger_entries_list' => 'array',
        'voucher_date' => 'date',
        'cheque_date' => 'date',
    ];


    protected $dates = [
        'voucher_date',
        'cheque_date'
    ];

    public static function createSales($request)
    {

        foreach ($request->Sales as $i => $data) {

            Sale::create([
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
