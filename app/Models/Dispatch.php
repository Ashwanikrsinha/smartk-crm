<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = ['dispatch_date'];

    public static function generateNumber(): string
    {
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->max('id') ?? 0;
        return 'DISP-' . $year . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function dispatchedBy()
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    public function items()
    {
        return $this->hasMany(DispatchItem::class, 'dispatch_id');
    }
}
