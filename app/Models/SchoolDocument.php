<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolDocument extends Model
{
    use HasFactory;

    protected $guarded = [];

    const TYPE_AADHAR  = 'aadhar';
    const TYPE_PAN     = 'pan';
    const TYPE_GST_CERT = 'gst_certificate';

    public static function types(): array
    {
        return [self::TYPE_AADHAR, self::TYPE_PAN, self::TYPE_GST_CERT];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
