<?php

namespace App\Models;

use App\Traits\ActiveTrait;
use App\Traits\ActorTrait;
use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    use ActiveTrait, ActorTrait;

    protected $table = 'ticket_statuses';

    const TICKET_STATUS_NEW = 'N';
    const IN_PROGRESS = 'I';
    const PENDING = 'P';
    const TICKET_STATUS_PENDING = 'P';
    const SOLVED = 'S';
    const TICKET_STATUS_DELETED = 'D';
    const TICKET_STATUS_MERGED = 'M';


    /**
     * Relationships
     */
    public function ticketHeaders()
    {
        return $this->hasMany('App\Models\TicketStatus', 'status', 'code');
    }


    /**
     * Scopes
     */
    public function scopeIsAction($query)
    {
        return $query->where('is_action', true);
    }
}
