<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $guarded = [];

    const STATUS_ACTIVE = 'A';
    const STATUS_DELETED = 'D';
    const STATUSES = [
        ApiKey::STATUS_ACTIVE => 'Active', 
        ApiKey::STATUS_DELETED => 'Deleted'
    ];


    /*
    |--------------------------------------------------------------------------
    | Accessor and Mutators
    |--------------------------------------------------------------------------
    | Accessors and mutators allow you to format Eloquent attribute values when 
    | you retrieve or set them on model instances.
    |
    | Write accessor and mutators below
    |
    */


    /*
    |--------------------------------------------------------------------------
    | Local Scopes
    |--------------------------------------------------------------------------
    | Local scopes allow you to define common sets of constraints that you may 
    | easily re-use throughout your application.
    |
    | Write local scopes below
    |
    */
    public function scopeActive($query)
    {
        return $query->where('status', ApiKey::STATUS_ACTIVE);
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Relationships allows you to define table relationships as methods 
    | on your Eloquent model classes. 
    |
    | Write relationships below 
    |
    */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
