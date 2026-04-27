<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['collected_at'];
    protected $casts = [
        'collected_at' => 'date',
    ];
    const MODE_CHEQUE = 'cheque';
    const MODE_NEFT   = 'neft';
    const MODE_UPI    = 'upi';
    const MODE_CASH   = 'cash';

    public static function paymentModes(): array
    {
        return [self::MODE_CHEQUE, self::MODE_NEFT, self::MODE_UPI, self::MODE_CASH];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    protected static function booted(): void
    {
        static::saved(function (Collection $collection) {
            $collection->invoice->recalculateCollections();
        });

        static::deleted(function (Collection $collection) {
            $collection->invoice->recalculateCollections();
        });
    }
}
