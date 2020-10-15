<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $table = 'ticket_attachments';
    protected $guarded = [];
    
    public function ticketHeader()
    {
        return $this->belongsTo('App\Models\TicketHeader', 'ticket_header_id', 'id');
    }
}
