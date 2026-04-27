<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pdc extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['cheque_date'];
    protected $casts = [
        'cheque_date' => 'date',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CLEARED = 'cleared';
    const STATUS_BOUNCED = 'bounced';

    public static function statuses(): array
    {
        return [self::STATUS_PENDING, self::STATUS_CLEARED, self::STATUS_BOUNCED];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * When a PDC is marked cleared/bounced, recalculate
     * the parent invoice's Collection (D) total.
     * Cleared PDCs count toward D; bounced/pending do not.
     */
    protected static function booted(): void
    {
        static::updated(function (Pdc $pdc) {
            // Only recalculate if status changed
            if ($pdc->wasChanged('status')) {
                $pdc->invoice->recalculateCollections();
            }
        });
    }
}
