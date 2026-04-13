<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class Bill extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    const SALE = 'sale';

    const PURCHASE = 'purchase';

    const WITH_PRICE = 'with-price';

    const WITHOUT_PRICE = 'without-price';

    protected $dates = [
        'bill_date',
        'delivery_date'
    ];
    protected $casts = [
        'bill_date' => 'date',
        'delivery_date' => 'date',
    ];


    public static function billNumber()
    {
        return self::doesntExist()
        ? 1001
        : self::max('bill_number') + 1;
    }


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function transport()
    {
        return $this->belongsTo(Transport::class, 'transport_id');
    }


    public function billItems()
    {
        return $this->hasMany(BillItem::class, 'bill_id');
    }

    public function attachments()
    {
        return $this->hasMany(BillAttachment::class, 'bill_id');
    }


    public function createBillItems($request){

        foreach ($request->products as $i => $product) {

            $this->billItems()->create([
                'product_id' => $product,
                'description' => $request->descriptions[$i],
                'unit_id' => $request->units[$i],
                'quantity' => $request->quantities[$i],
                'rate' => $request->rates[$i],
                'amount' => $request->amounts[$i],
            ]);
        }
    }

    public function createBillAttachments($request){

        foreach ($request->attachments as $attachemnt) {
            $this->attachments()->create([
                'filename' => $attachemnt
           ]);
        }
    }


}
