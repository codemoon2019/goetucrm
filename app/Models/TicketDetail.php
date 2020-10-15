<?php

namespace App\Models;

use App\Traits\ActiveTrait;
use App\Traits\ActorTrait;
use Illuminate\Database\Eloquent\Model;

class TicketDetail extends Model
{
    use ActiveTrait, ActorTrait;
    
    protected $table = 'ticket_details';
    protected $guarded = [];

    /**
     * Ticket header has many Assignees (User)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function assignees()
    {
        return $this->belongsToMany("App\Models\User", 'ticket_assignee', 
            "ticket_header_id", "user_id")->withTimestamps();
    }

    public function attachments()
    {
        return $this->hasMany("App\Models\TicketDetailsAttachment", 'ticket_detail_id', 'id');
    }
}
