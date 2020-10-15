<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketDetailsAttachment extends Model
{
    protected $table = 'ticket_detail_attachments';
    protected $guarded = [];
    
    public function ticketDetail()
    {
        return $this->belongsTo('App\Models\TicketDetail', 'ticket_detail_id', 'id');
    }
}
