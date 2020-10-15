<?php

namespace App;

use App\Models\Banner;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
//use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    //use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name','last_name', 'email_address', 'password','username','user_type_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    /*
    |--------------------------------------------------------------------------
    | Accessor and Mutators
    |--------------------------------------------------------------------------
    |
    | Write accessor and mutators below
    |
    */
    public function getBannersAttribute()
    {
        $readBannerIds = session('read_banners') ?? [];
        return Banner::whereNotIn('id', $readBannerIds)
            ->active()
            ->showing()
            ->whereHas('bannerViewers', function($query) {
                $query->viewableBy($this);
            })
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Local Scopes
    |--------------------------------------------------------------------------
    |
    | Write local scopes below
    |
    */


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Write relationships below 
    |
    */
    public function apiKeys()
    {
        return $this->hasMany('App\Models\ApiKey');
    }
}

