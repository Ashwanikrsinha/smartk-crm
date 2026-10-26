<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $dates = ['start_date', 'end_date'];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'username');
    }

    public function getMonthLabelAttribute(): string
    {
        return date('F', mktime(0, 0, 0, $this->month, 1)) . ' ' . $this->year;
    }

    public static function calculateAchievedForPeriod(
        array $userIds,
        ?int $month = null,
        ?int $year = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?array $sessionRange = null
    ): float {
        $invoices = Invoice::whereIn('user_id', $userIds)
            ->where('status', Invoice::STATUS_APPROVED)
            ->where('billing_amount', '!=', 0)
            ->with(['billingEntries' => function ($q) use ($month, $year, $dateFrom, $dateTo, $sessionRange) {
                if ($month && $year) {
                    $q->whereYear('billed_at', $year)->whereMonth('billed_at', $month);
                } elseif ($dateFrom && $dateTo) {
                    $q->whereBetween('billed_at', [$dateFrom, $dateTo]);
                } elseif ($sessionRange) {
                    $q->whereBetween('billed_at', [$sessionRange['start_date'], $sessionRange['end_date']]);
                }
            }])
            ->get();

        $totalAchieved = 0.0;

        foreach ($invoices as $inv) {
            $totalBilled = (float) $inv->billing_amount;
            $gstAmount   = (float) ($inv->gst_amount ?? 0);
            $netRatio    = $totalBilled > 0 ? max(0, $totalBilled - $gstAmount) / $totalBilled : 1.0;

            // Check if this invoice has billing entries in DB
            $hasAnyEntries = BillingEntry::where('invoice_id', $inv->id)->exists();

            if ($hasAnyEntries) {
                // Sum entries matching the requested period
                $periodBilled = (float) $inv->billingEntries->sum('billed_amount');
                if ($periodBilled != 0.0) {
                    $totalAchieved += ($periodBilled * $netRatio);
                }
            } else {
                // Fallback for invoices without billing_entries: use invoice_date
                $matchesPeriod = false;
                if ($month && $year) {
                    $matchesPeriod = ((int) $inv->invoice_date->format('Y') === $year && (int) $inv->invoice_date->format('n') === $month);
                } elseif ($dateFrom && $dateTo) {
                    $invDate = $inv->invoice_date->format('Y-m-d');
                    $matchesPeriod = ($invDate >= $dateFrom && $invDate <= $dateTo);
                } elseif ($sessionRange) {
                    $invDate = $inv->invoice_date->format('Y-m-d');
                    $matchesPeriod = ($invDate >= $sessionRange['start_date'] && $invDate <= $sessionRange['end_date']);
                }

                if ($matchesPeriod) {
                    $totalAchieved += max(0, $totalBilled - $gstAmount);
                }
            }
        }

        return round($totalAchieved, 2);
    }

    public function achievedAmount(): float
    {
        return self::calculateAchievedForPeriod([$this->user_id], (int) $this->month, (int) $this->year);
    }

    public function achievementPercent(): int
    {
        if ($this->target_amount <= 0) return 0;
        return (int) min(round(($this->achievedAmount() / $this->target_amount) * 100), 999);
    }

}

