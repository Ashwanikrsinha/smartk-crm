<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $dates = ['from_date', 'to_date'];
    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];



    public static function leaveNumber()
    {
        return self::doesntExist()
            ? 1001
            : self::max('leave_number') + 1;
    }


    public function scopeApproved($query)
    {

        return $query->where('status', 'approved');
    }


    // public static function types()
    // {
    //     return ['personal leave', 'sick leave', 'casual leave'];
    // }

    public static function leaveStatus()
    {
        return ['approved', 'disapproved', 'pending'];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'username', 'reportive_id');
    }

    public function scopeUsersOnLeaveToday($query)
    {

        return $query->approved()
            ->where(function ($q) {
                $q->whereDate('from_date', date('Y-m-d'))
                    ->orWhereDate('to_date', '>=', date('Y-m-d'));
            })
            ->orderBy('id', 'DESC');
    }
    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static function types(): array
    {
        return ['earned leave', 'maternity leave','personal leave', 'sick leave', 'casual leave'];
    }

    public static function statuses(): array
    {
        return [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED];
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function getDaysAttribute(): int
    {
        return $this->from_date->diffInDays($this->to_date) + 1;
    }
}
