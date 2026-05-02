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
                // If cleared, pass the amount. If it was cleared and now it's not, pass negative.
                $amount = 0;
                if ($pdc->status === self::STATUS_CLEARED) {
                    $amount = $pdc->amount;
                } elseif ($pdc->getOriginal('status') === self::STATUS_CLEARED) {
                    $amount = -$pdc->amount;
                }

                $pdc->invoice->recalculateCollections($amount, [
                    'payment_mode'     => 'pdc',
                    'reference_number' => $pdc->cheque_number,
                    'remarks'          => "PDC {$pdc->pdc_label} marked as {$pdc->status}",
                ]);
            }
        });
    }
}
