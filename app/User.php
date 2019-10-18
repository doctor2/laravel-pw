<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;


    protected static function boot()
    {
        parent::boot();

        static::created(function($user){
            $user->balance()->create(['balance' => 500]);
        });
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','banned'
    ];

    protected $appends= [
        'hasBan',
        'isAdmin',
        'currentBalance'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getIsAdminAttribute()
    {
        return in_array($this->attributes['email'],['brath1@example.org']);
    }

    public function getCurrentBalanceAttribute()
    {
        return $this->balance->balance;
    }

    public function setBannedAttribute($value)
    {
        $this->attributes['banned'] = ($value=='on');
    }

    public function getHasBanAttribute()
    {
        return $this->attributes['banned'] ? 'yes' : 'no';
    }

    public function balance()
    {
        return $this->hasOne(Balance::class);
    }
}
