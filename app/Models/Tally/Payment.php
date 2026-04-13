<?php

namespace App\Models\Tally;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;


    protected $dates = [
        'voucher_date',
        'cheque_date'
    ];
    protected $casts = [
        'voucher_date' => 'date',
        'cheque_date' => 'date',
    ];

    public static function createPayments($request)
    {


        foreach ($request->Payment as $i => $data) {


            Payment::create([
                'voucher_number' => $data['VoucherNo'],
                'reference_number' => $data['ReferenceNo'],
                'reference_type' => $data['TypeofRefNo'],
                'voucher_type' => $data['VoucherType'],
                'voucher_master_id' => $data['VoucherMasterId'],
                'voucher_alter_id' => $data['VoucherAlterId'],
                'company_name' => $data['TallyCompanyName'],
                'voucher_date' => date('Y-m-d', strtotime($data['Date'])),
                'amount' => $data['Amount'],
                'payment_mode' => $data['PaymentMode'],
                'cheque_number' => $data['ChequeNumber'],
                'cheque_date' => date('Y-m-d', strtotime($data['ChequeDate'])),
                'bank_name' => $data['BankName'],
                'debit_ledger' => $data['DebitLedger'],
                'credit_ledger' => $data['CreditLedger']
             ]);


         }

    }
}
