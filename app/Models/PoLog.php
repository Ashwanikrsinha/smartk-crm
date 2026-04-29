<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount'                => 'decimal:2',
        'snapshot_po_amount'    => 'decimal:2',
        'snapshot_billed_amount' => 'decimal:2',
        'snapshot_collected'    => 'decimal:2',
        'snapshot_outstanding'  => 'decimal:2',
        'snapshot_pending_po'   => 'decimal:2',
    ];

    const ACTION_BILLED       = 'billed';
    const ACTION_COLLECTED    = 'collected';
    const ACTION_PDC_CLEARED  = 'pdc_cleared';
    const ACTION_APPROVED     = 'approved';
    const ACTION_REJECTED     = 'rejected';
    const ACTION_STATUS       = 'status_change';

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            self::ACTION_BILLED      => 'Billing Entry',
            self::ACTION_COLLECTED   => 'Collection Entry',
            self::ACTION_PDC_CLEARED => 'PDC Cleared',
            self::ACTION_APPROVED    => 'PO Approved',
            self::ACTION_REJECTED    => 'PO Rejected',
            self::ACTION_STATUS      => 'Status Changed',
            default                  => ucfirst($this->action),
        };
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            self::ACTION_BILLED      => 'info',
            self::ACTION_COLLECTED   => 'success',
            self::ACTION_PDC_CLEARED => 'warning',
            self::ACTION_APPROVED    => 'primary',
            self::ACTION_REJECTED    => 'danger',
            default                  => 'secondary',
        };
    }

    /**
     * Create a log entry and snapshot the invoice state.
     */
    public static function record(Invoice $invoice, string $action, array $extra = []): self
    {
        // Re-read fresh to get latest computed totals
        $invoice->refresh();

        return self::create(array_merge([
            'invoice_id'             => $invoice->id,
            'user_id'                => auth()->id(),
            'action'                 => $action,
            'snapshot_po_amount'     => $invoice->amount,
            'snapshot_billed_amount' => $invoice->billing_amount,
            'snapshot_collected'     => $invoice->collected_amount,
            'snapshot_outstanding'   => $invoice->outstanding_amount,
            'snapshot_pending_po'    => $invoice->pending_po_amount,
        ], $extra));
    }
}
