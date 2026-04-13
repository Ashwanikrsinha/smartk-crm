<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $dates = [
        'quotation_date', 'follow_date'
    ];
    protected $casts = [
        'quotation_date' => 'date',
        'follow_date' => 'date',
    ];


    const FOLLOW_UP = 'follow up';

    const NOT_INTERESTED = 'not interested';

    const SEND_PERFORMA = 'send performa';


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }

    public function QuotationItems()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id');
    }

    public function attachments()
    {
        return $this->hasMany(QuotationAttachment::class, 'quotation_id');
    }

    public static function quotationNumber()
    {
        return self::doesntExist()
        ? 1001
        : self::max('quotation_number') + 1;
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
            self::SEND_PERFORMA
        ];
    }

    public function createQuotationItems($request){

        foreach ($request->products as $i => $product) {
            $this->quotationItems()->create([
                'product_id' => $product,
                'description' => $request->descriptions[$i],
                'unit_id' => $request->units[$i],
                'quantity' => $request->quantities[$i],
                'rate' => $request->rates[$i]
            ]);
        }
    }

    public function createQuotationAttachments($request){

        foreach ($request->attachments as $attachemnt) {
            $this->attachments()->create([
                'filename' => $attachemnt,
           ]);
        }
    }

}
