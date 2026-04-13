<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $timestamps = false;

    protected $casts = [
        'is_disable'    => 'boolean',
        'inactive_date' => 'datetime',
    ];


    public function role(){
        return $this->belongsTo(Role::class, 'role_id');
    }


    public function hasPermission($name){

       return isset($this->role->permissions) && $this->role->permissions->where('name', $name)->count() > 0
       ? true
       : false;

    }


    public function department(){

        return $this->belongsTo(Department::class, 'department_id');

    }

    public static function maritalStatus(){

        return ['single', 'married'];

    }

    public function scopeActive($query){

        return $query->where('is_disable', 0);

    }

    public function reportiveTo(){

        return $this->belongsTo(User::class, 'reportive_id');

    }

    public function logs(){

        return $this->hasMany(Log::class, 'user_id')->orderBy('login_time', 'DESC');

    }

    public function customers(){

        return $this->hasMany(Customer::class, 'user_id');

    }

    public function visits(){

        return $this->hasMany(Visit::class, 'user_id');
    }

    public function todayVisits(){

        return $this->visits()->where('visit_date', date('Y-m-d'));
    }

    public function yesterdayVisits(){

        return $this->visits()->where('visit_date', date('Y-m-d', strtotime('-1 days')));
    }

    public function lastSevenDayVisits(){

        return $this->visits()
        ->whereBetween('visit_date', [
            date('Y-m-d', strtotime('-7 days')),
            date('Y-m-d')
        ]);

    }

    public function currentMonthVisits(){
        return $this->visits()
        ->whereYear('visit_date', date('Y'))
        ->whereMonth('visit_date', date('m'));
    }

    public function lastMonthVisits(){

        return $this->visits()
        ->whereBetween('visit_date', [
            date('Y-m-d', strtotime('first day of last month')),
            date('Y-m-d', strtotime('last day of last month'))
        ]);
    }

    public static function createUser($data){

        $data['password'] = bcrypt($data['password']);
        $data['is_disable'] = isset($data['is_disable']) ? 1 : 0;

        self::create($data);

    }


}
