<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    protected $table = 'business_types';
    protected $guarded = [];

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
    


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Write relationships below 
    |
    */
    public function leads()
    {
        return $this->hasMany(Lead::class, 'business_type_code', 'code');
    }

    public function merchants()
    {
        return $this->hasMany(Partner::class, 'business_type_code', 'code');
    }

    public function prospects()
    {
        return $this->hasMany(Prospect::class, 'business_type_code', 'code');
    }

    public function supplierLeads()
    {
        return $this->hasMany(SupplierLead::class, 'business_type_code', 'code');
    }
}
