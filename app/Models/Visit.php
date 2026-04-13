<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $dates = ['visit_date', 'follow_date'];
    protected $casts = [
        'visit_date' => 'date',
        'follow_date' => 'date',
    ];

    const FOLLOW_UP = 'follow up';

    const NOT_INTERESTED = 'not interested';

    const SEND_QUOTATION = 'send quotation';

    const SEND_PERFORMA = 'send performa';

    const MATURE = 'mature';


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'username');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }


    public function purpose()
    {
        return $this->belongsTo(Purpose::class, 'purpose_id');
    }

    public function quotation()
    {
        return $this->hasOne(Quotation::class, 'visit_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'visit_id');
    }

    public function visitItems()
    {
        return $this->hasMany(VisitItem::class, 'visit_id');
    }

    public function attachments()
    {
        return $this->hasMany(VisitAttachment::class, 'visit_id');
    }

    public static function followTypes()
    {
        return ['personal', 'phone', 'email'];
    }

    public static function levels()
    {
        return ['hot', 'cold', 'warm'];
    }

    public static function status()
    {
        return [
            self::NOT_INTERESTED,
            self::FOLLOW_UP,
            self::MATURE,
            self::SEND_QUOTATION,
            self::SEND_PERFORMA
        ];
    }


    public static function visitNumber()
    {
        return self::doesntExist()
            ? 1001
            : self::max('visit_number') + 1;
    }

    public function createVisitItems($request)
    {

        foreach ($request->products as $i => $product) {
            $this->visitItems()->create([
                'product_id' => $product,
                'description' => $request->descriptions[$i],
                'unit_id' => $request->units[$i],
                'quantity' => $request->quantities[$i],
                'rate' => $request->rates[$i]
            ]);
        }
    }

    public function createVisitAttachments($request)
    {

        foreach ($request->attachments as $attachemnt) {
            $this->attachments()->create([
                'filename' => $attachemnt,
            ]);
        }
    }
}
