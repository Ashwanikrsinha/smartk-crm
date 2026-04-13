<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public $timestamps = false;

    protected $dates = ['created_at'];
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(){

        return $this->belongsTo(User::class, 'user_id')->select('id', 'username');

    }

}
