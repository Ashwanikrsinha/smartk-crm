<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingEntry extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['billed_at'];

    const SOURCE_CRM    = 'crm';    // Raised via CRM bill module
    const SOURCE_MANUAL = 'manual'; // Tally / offline entry by Accounts

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    // -------------------------------------------------------
    // Auto-recalculate invoice B (billing_amount) on save/delete
    // -------------------------------------------------------

    protected static function booted(): void
    {
        static::saved(function (BillingEntry $entry) {
            $entry->invoice->recalculateBilling();
        });

        static::deleted(function (BillingEntry $entry) {
            $entry->invoice->recalculateBilling();
        });
    }
}
