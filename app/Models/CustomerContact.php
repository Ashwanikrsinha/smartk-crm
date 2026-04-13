<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerContact extends Model
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
          ->join('customers', 'customers.id', 'customer_contacts.customer_id')
          ->join('designations', 'designations.id', 'customer_contacts.designation_id')
          ->select('customer_contacts.*', 'designations.name AS designation', 'customers.id', 'customers.name');

    }


}
