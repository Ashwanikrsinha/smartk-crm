<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $dates = ['deadline_time', 'created_at', 'completed_at'];
    protected $casts = [
        'deadline_time' => 'datetime',
        'created_at' => 'datetime',
        'completed_at' => 'datetime',
    ];



    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id')->select('id', 'username');
    }

    public function assignor()
    {
        return $this->belongsTo(User::class, 'assignor_id')->select('id', 'username');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'task_id')->orderBy('id', 'DESC');
    }


    public static function taskNumber()
    {
        return self::doesntExist()
        ? 1001
        : self::max('task_number') + 1;
    }

}

