<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class, 'dispatch_id');
    }

    public function invoiceItem()
    {
        return $this->belongsTo(InvoiceItem::class, 'invoice_item_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
