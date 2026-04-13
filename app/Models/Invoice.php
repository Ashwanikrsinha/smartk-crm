<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $dates = [
        'invoice_date',
    ];
    protected $casts = [
        'invoice_date' => 'date',
    ];

    const FOLLOW_UP = 'follow up';

    const NOT_INTERESTED = 'not interested';

    const MATURE = 'mature';


    public static function invoiceNumber()
    {
        return self::doesntExist()
        ? 1001
        : self::max('invoice_number') + 1;
    }


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }


    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function attachments()
    {
        return $this->hasMany(InvoiceAttachment::class, 'invoice_id');
    }


    public static function followTypes()
    {
        return ['personal', 'phone', 'email'];
    }

    public static function status()
    {
        return [
            self::NOT_INTERESTED,
            self::FOLLOW_UP,
            self::MATURE
        ];
    }


    public function createInvoiceItems($request){

        foreach ($request->products as $i => $product) {

            $this->invoiceItems()->create([
                'product_id' => $product,
                'description' => $request->descriptions[$i],
                'unit_id' => $request->units[$i],
                'quantity' => $request->quantities[$i],
                'rate' => $request->rates[$i],
                'amount' => $request->amounts[$i],
            ]);
        }
    }

    public function createInvoiceAttachments($request){

        foreach ($request->attachments as $attachemnt) {
            $this->attachments()->create([
                'filename' => $attachemnt
           ]);
        }
    }


}
