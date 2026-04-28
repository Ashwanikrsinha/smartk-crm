<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $dates = ['invoice_date', 'approved_at', 'delivery_due_date','collected_at','school_mail_sent_at','sp_mail_sent_at','accounts_mail_sent_at'];
    protected $casts = [
        'invoice_date'   => 'date',
        'approved_at'    => 'datetime',
        'school_mail_sent_at' => 'datetime',
        'sp_mail_sent_at' => 'datetime',
        'accounts_mail_sent_at' => 'datetime',
        'delivery_due_date' => 'date',
        'collected_at' => 'date',
    ];



    const STATUS_DRAFT     = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';

    // ── Relationships ───────────────────────────────────────

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function attachments()
    {
        return $this->hasMany(InvoiceAttachment::class, 'invoice_id');
    }

    public function pdcs()
    {
        return $this->hasMany(Pdc::class, 'invoice_id');
    }

    public function collections()
    {
        return $this->hasMany(Collection::class, 'invoice_id');
    }

    public function billingEntries()
    {
        return $this->hasMany(BillingEntry::class, 'invoice_id');
    }

    /** CRM-generated bills (via Bill module) */
    public function bills()
    {
        return $this->hasMany(Bill::class, 'invoice_id');
    }

    // ── Status Helpers ──────────────────────────────────────

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
        ];
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }
    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED]);
    }

    // ── Financial Calculations A / B / C / D / E ────────────

    /**
     * A — Total PO Amount (what SP quoted)
     */
    public function getPoAmountAttribute(): float
    {
        return (float) $this->amount;
    }

    /**
     * B — Total Billed Amount
     * = Sum of CRM billing entries + Manual billing entries by Accounts
     * Both feed into billing_entries table.
     * CRM bills also create a billing_entry record automatically (source='crm').
     */
    public function getBillingAmountAttribute(): float
    {
        return (float) ($this->attributes['billing_amount'] ?? 0);
    }

    /**
     * C — Pending PO Amount (A - B)
     */
    public function getPendingPoAmountAttribute(): float
    {
        return $this->po_amount - $this->billing_amount;
    }

    /**
     * D — Total Collected
     * = Manual collection entries + Cleared PDC cheques
     */
    public function getTotalCollectedAttribute(): float
    {
        return (float) ($this->attributes['collected_amount'] ?? 0);
    }

    /**
     * E — Outstanding Amount (B - D)
     */
    public function getOutstandingAmountAttribute(): float
    {
        return $this->billing_amount - $this->total_collected;
    }

    // ── Recalculation Methods (called by model observers) ───

    /**
     * Recalculate B (billing_amount) and C (pending_po_amount).
     * Sources:
     *   - billing_entries table (both 'crm' and 'manual' sources)
     * Called by: BillingEntry::booted() on saved/deleted
     */
    public function recalculateBilling(): void
    {
        $totalBilled = $this->billingEntries()->sum('billed_amount');

        $this->updateQuietly([
            'billing_amount'    => $totalBilled,
            'pending_po_amount' => max($this->amount - $totalBilled, 0),
            // Also recalculate E since B changed
            'outstanding_amount' => $totalBilled - $this->attributes['collected_amount'],
        ]);
    }

    /**
     * Recalculate D (collected_amount) and E (outstanding_amount).
     * Sources:
     *   - collections table (manual Accounts entries)
     *   - pdcs table where status = 'cleared' (advance PDC amounts)
     * Called by: Collection::booted() and Pdc status update
     */
    public function recalculateCollections(): void
    {
        $manualCollections = $this->collections()->sum('collected_amount');
        $clearedPdcs       = $this->pdcs()->where('status', 'cleared')->sum('amount');

        $totalCollected = $manualCollections + $clearedPdcs;

        $this->updateQuietly([
            'collected_amount'   => $totalCollected,
            'outstanding_amount' => $this->attributes['billing_amount'] - $totalCollected,
        ]);
    }

    public static function invoiceNumber(): int
    {
        return self::doesntExist() ? 1001 : self::max('invoice_number') + 1;
    }

    public static function generatePoNumber(): string
    {
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->max('id') ?? 0;
        $seq  = str_pad($last + 1, 4, '0', STR_PAD_LEFT);
        return "PO-{$year}-{$seq}";
    }

    public static function followTypes(): array
    {
        return ['personal', 'phone', 'email'];
    }

    public function createInvoiceItems($request): void
    {
        foreach ($request->products as $i => $product) {
            $this->invoiceItems()->create([
                'product_id'  => $product,
                'description' => $request->descriptions[$i] ?? '',
                'unit_id'     => $request->units[$i],
                'quantity'    => $request->quantities[$i],
                'rate'        => $request->rates[$i],
                'discount'    => $request->discounts[$i],
                'amount'      => $request->amounts[$i],
            ]);
        }
    }

    public function createInvoiceAttachments($request): void
    {
        foreach ($request->attachments as $attachment) {
            $this->attachments()->create(['filename' => $attachment]);
        }
    }

    public function createPdcs($request): void
    {
        if (!$request->has('pdc_dates')) return;

        foreach ($request->pdc_dates as $i => $date) {
            if (empty($date)) continue;
            $this->pdcs()->create([
                'pdc_label'     => 'PDC ' . ($i + 1),
                'cheque_date'   => $date,
                'cheque_number' => $request->pdc_cheque_numbers[$i],
                'bank_name'     => $request->pdc_bank_names[$i] ?? null,
                'amount'        => $request->pdc_amounts[$i],
                'status'        => 'pending',
            ]);
        }
    }


}
