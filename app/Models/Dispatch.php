<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['dispatch_date', 'edited_at'];

    protected $casts = [
        'dispatch_date' => 'date',
        'edited_at'     => 'datetime',
    ];

    public static function generateNumber(): string
    {
        if (date('n') >= 4) {
            $startYear = date('Y');
        } else {
            $startYear = date('Y') - 1;
        }
    
        $endYearShort = substr($startYear + 1, -2);
    
        $last = self::whereYear('created_at', date('Y'))->max('id') ?? 0;
        $seq = str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    
        return "DISP-{$startYear}-{$endYearShort}-{$seq}";
    }
    // public static function generateNumber(): string
    // {
    //     $year = date('Y');
    //     $last = self::whereYear('created_at', $year)->max('id') ?? 0;
    //     return 'DISP-' . $year . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    // }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function dispatchedBy()
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    public function editedBy()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function items()
    {
        return $this->hasMany(DispatchItem::class, 'dispatch_id');
    }

    /**
     * A dispatch can only be corrected once. Once edited_by is set,
     * this returns true and the edit option should no longer be offered.
     */
    public function isEdited(): bool
    {
        return !is_null($this->edited_by);
    }
}