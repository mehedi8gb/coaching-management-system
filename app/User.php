<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable; 
use Illuminate\Foundation\Auth\User as Authenticatable; 

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
 
    public static $item ="23876323";  //23876323 //22014245 //23876323

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'username', 'phone', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function student(){
        return $this->belongsTo('App\SmStudent', 'id', 'user_id');
    }
    public function staff(){
        return $this->belongsTo('App\SmStaff', 'id', 'user_id');
    }
    public function parent(){
        return $this->belongsTo('App\SmParent', 'id', 'user_id');
    }

    public function school(){
        return $this->belongsTo('App\SmSchool', 'school_id', 'id');
    }

    public function roles(){
        return $this->belongsTo('Modules\RolePermission\Entities\InfixRole', 'role_id', 'id');
    }
}