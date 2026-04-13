<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierContact extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $dates = ['birth_date', 'marriage_date'];
    protected $casts = [
        'birth_date' => 'date',
        'marriage_date' => 'date',
    ];


    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id')->withDefault(['name' => '']);
    }

    public function scopeWithOccassionsToday($query){

       return $query->whereRaw(
            'DAY(birth_date) = DAY(CURRENT_DATE())
            AND MONTH(birth_date) = MONTH(CURRENT_DATE())
            OR DAY(marriage_date) = DAY(CURRENT_DATE())
            AND MONTH(marriage_date) = MONTH(CURRENT_DATE())'
          )
          ->join('suppliers', 'suppliers.id', 'supplier_contacts.supplier_id')
          ->join('designations', 'designations.id', 'supplier_contacts.designation_id')
          ->select('supplier_contacts.*', 'designations.name AS designation', 'suppliers.id', 'suppliers.name');

    }


}
