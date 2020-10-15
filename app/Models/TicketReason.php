<?php

namespace App\Models;

use App\Traits\ActiveTrait;
use App\Traits\ActorTrait;
use Illuminate\Database\Eloquent\Model;

class TicketReason extends Model
{
	use ActiveTrait, ActorTrait;

    const STATUS_ACTIVE = 'A';
    const STATUS_DELETED = 'D';

    protected $guarded = [];

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
	public function department()
	{
		return $this->belongsTo(UserType::class, 'department_id');
	}

	public function ticketPriority()
	{
		return $this->belongsTo(TicketPriority::class, 'ticket_priority_code', 'code');
	}
}
