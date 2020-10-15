<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentProcessor extends Model
{
     protected $table = 'payment_processors';

    const STATUS_ACTIVE = 'A';
    const STATUS_DELETED = 'D';
    const STATUSES = [
        self::STATUS_ACTIVE => 'Active', 
        self::STATUS_DELETED => 'Deleted'
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessor and Mutators
    |--------------------------------------------------------------------------
    |
    | Write accessor and mutators below
    |
    */


    /*
    |--------------------------------------------------------------------------
    | Local Scopes
    |--------------------------------------------------------------------------
    |
    | Write local scopes below
    |
    */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
    
     
}
