<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;

    protected $dates = ['birth_date', 'marriage_date', 'joining_date', 'resign_date'];
    protected $casts = [
        'birth_date' => 'date',
        'marriage_date' => 'date',
        'joining_date' => 'date',
        'resign_date' => 'date',
    ];


    public function department(){

        return $this->belongsTo(Department::class, 'department_id');

    }

    public static function maritalStatus(){

        return ['single', 'married'];

    }



    public function scopeWithOccassionsToday($query){

        return $query->select('id', 'name', 'birth_date', 'marriage_date', 'department_id')
            ->whereRaw('DAY(birth_date) = DAY(CURRENT_DATE())
                AND MONTH(birth_date) = MONTH(CURRENT_DATE())
                OR DAY(marriage_date) = DAY(CURRENT_DATE())
                AND MONTH(marriage_date) = MONTH(CURRENT_DATE())')
            ->orderBy('id', 'DESC');
    }


}
