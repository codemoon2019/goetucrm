<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierLead extends Model
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
    public function getFormattedIdAttribute()
    {
        return 'SL1' . str_pad($this->id, '7', '0', STR_PAD_LEFT);
    }

    public function getFullBusinessAddressAttribute()
    {
        return "{$this->business_address}, {$this->city}, " . 
               "{$this->state->name}, {$this->zip}, " .
               "{$this->country->name}";
    }

    public function getPartnerNameAttribute()
    {
        return "{$this->partner->connectedUser->full_name}";
    }

    /*
    |--------------------------------------------------------------------------
    | Local Scopes
    |--------------------------------------------------------------------------
    |
    | Write local scopes below
    |
    */
    public function scopeWhereCompany($query, $companyId)
    {
        if ($companyId == null || $companyId == -1)
            return $query;

        return $query->whereHas('partner', function($query) use ($companyId) {
            $query->where('company_id', $companyId);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Write relationships below
    |
    */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    public function contacts()
    {
        return $this->hasMany(SupplierLeadContact::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function products()
    {
        return $this->hasMany(SupplierLeadProduct::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
