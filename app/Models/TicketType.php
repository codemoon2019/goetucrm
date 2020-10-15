<?php

namespace App\Models;

use App\Traits\ActiveTrait;
use App\Traits\ActorTrait;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use ActiveTrait, ActorTrait;
    
    const STATUS_ACTIVE = "A";
    const STATUS_DELETED = "D";

    protected $table = 'ticket_types';
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
    public function scopeWhereCompany($query, $companyId)
    {
        if ($companyId == -1 || $companyId == null) {
            return $query;
        }

        return $query->where('company_id', $companyId);
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Write relationships below 
    |
    */
    public function ticketReasons()
    {
        return $this->hasMany(TicketReason::class);
    }
}
