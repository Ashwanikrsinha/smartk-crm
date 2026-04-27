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

    public function achievedAmount(): float
    {
        return (float) Invoice::where('user_id', $this->user_id)
            ->where('status', Invoice::STATUS_APPROVED)
            ->whereMonth('invoice_date', $this->month)
            ->whereYear('invoice_date',  $this->year)
            ->sum('amount');
    }

    public function achievementPercent(): int
    {
        if ($this->target_amount <= 0) return 0;
        return (int) min(round(($this->achievedAmount() / $this->target_amount) * 100), 999);
    }

}

