<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $dates = ['login_time', 'logout_time'];
    protected $casts = [
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
    ];

    protected $guarded = [];


}
