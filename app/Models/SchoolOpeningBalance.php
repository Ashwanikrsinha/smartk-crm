<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolOpeningBalance extends Model
{
    use HasFactory;

    protected $table = 'school_opening_balances';

    protected $guarded = [];

    protected $casts = [
        'opening_amount'      => 'decimal:2',
        'opening_billed'      => 'decimal:2',
        'opening_collected'   => 'decimal:2',
        'opening_outstanding' => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /** Sales person who did the original sales */
    public function salesPerson()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Admin/Accounts user who entered this record */
    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function collections()
    {
        return $this->hasMany(OpeningBalanceCollection::class, 'opening_balance_id')
                    ->orderByDesc('collected_at');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Recompute opening_collected and opening_outstanding from child records.
     * Called by OpeningBalanceCollection observer on saved/deleted.
     */
    public function recalculateCollected(): void
    {
        $totalCollected = $this->collections()->sum('collected_amount');

        $this->updateQuietly([
            'opening_collected'   => $totalCollected,
            'opening_outstanding' => $this->opening_amount >= 0
                ? max(0, $this->opening_amount - $totalCollected)
                : ($this->opening_amount - $totalCollected),
        ]);
    }

    /**
     * Human-readable session label, e.g. "2025-26"
     */
    public function sessionLabel(): string
    {
        $end = ($this->session_year + 1) % 100;
        return $this->session_year . '-' . str_pad($end, 2, '0', STR_PAD_LEFT);
    }

    // ── Static Session Helpers ───────────────────────────────────────────────

    /**
     * Return the session_year for the current academic session.
     * Session = July of year to June of year+1.
     */
    public static function currentSessionYear(): int
    {
        return now()->month >= 7 ? now()->year : now()->year - 1;
    }

    /**
     * Return [start_date, end_date] for a given session_year.
     */
    public static function sessionDateRange(int $year): array
    {
        return [
            'start' => "{$year}-07-01",
            'end'   => ($year + 1) . '-06-30',
        ];
    }

    /**
     * Detect the session_year from a request filter value.
     * Accepts 'YYYY-YYYY', 'YYYY', or null (defaults to current).
     */
    public static function sessionYearFromFilter(?string $filter): int
    {
        if ($filter && preg_match('/^(\d{4})-(\d{4})$/', $filter, $m)) {
            return (int) $m[1];
        }
        if ($filter && preg_match('/^\d{4}$/', $filter)) {
            return (int) $filter;
        }
        return self::currentSessionYear();
    }

    /**
     * Scope query to all active opening balances for a given session.
     * Includes exact session_year records, plus any uncollected balances from past sessions
     * that haven't been superseded by a newer record for that school.
     */
    public function scopeForSession($query, int $sessionYear)
    {
        return $query->where(function ($q) use ($sessionYear) {
            $q->where('session_year', $sessionYear)
              ->orWhere(function ($pastQ) use ($sessionYear) {
                  $pastQ->where('session_year', '<', $sessionYear)
                        ->where('opening_outstanding', '!=', 0)
                        ->whereNotIn('customer_id', function ($sub) use ($sessionYear) {
                            $sub->select('customer_id')
                                ->from('school_opening_balances')
                                ->where('session_year', $sessionYear);
                        });
              });
        });
    }
}
