<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierLeadProduct extends Model
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
