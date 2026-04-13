<?php

namespace App\Models\Tally;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public $timestamps = false;

    public static function createLedgers($request)
    {
        
        foreach ($request->LedgerHeader as $i => $data) {
        
             Ledger::create([
                'company_name' => $data['CompanyName'],
                'ledger_name' => $data['LedgerName'],
                'master_id' => $data['MASTERID'],
                'ledger_id' => $data['LedgerId'] ?? null,
                'ledger_group' => $data['LedgerGroup'],
                'state' => $data['State'] ?? null,
                'gst_type' => $data['GSTRegType'] ?? null
             ]);
 
         }
         
    }
}
