<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierLeadContact extends Model
{
    protected $guarded = [];

    /*
    |--------------------------------------------------------------------------
    | Accessor and Mutators
    |--------------------------------------------------------------------------
    |
    | Write accessor and mutators below
    |
    */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
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
    public function supplierLead()
    {
        return $this->belongsTo(SupplierLead::class);
    }
}
