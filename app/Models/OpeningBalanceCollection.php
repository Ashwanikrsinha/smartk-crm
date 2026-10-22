<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OpeningBalanceCollection extends Model
{
    use HasFactory;

    protected $table = 'opening_balance_collections';

    protected $guarded = [];

    protected $casts = [
        'collected_at'     => 'date',
        'collected_amount' => 'decimal:2',
        'billed_amount'    => 'decimal:2',
        'gst_amount'       => 'decimal:2',
    ];

    public function isReturn(): bool
    {
        return $this->payment_mode === 'return';
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function openingBalance()
    {
        return $this->belongsTo(SchoolOpeningBalance::class, 'opening_balance_id');
    }

    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    // ── Observer — auto-recompute parent totals ───────────────────────────────

    protected static function booted(): void
    {
        static::saved(function (self $model) {
            $model->openingBalance?->recalculateCollected();
        });

        static::deleted(function (self $model) {
            $model->openingBalance?->recalculateCollected();
        });
    }
}
