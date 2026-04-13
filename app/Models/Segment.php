<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Segment::class, 'category_id')
           ->withDefault(['name' => 'Main Category']);
    }
   


}
