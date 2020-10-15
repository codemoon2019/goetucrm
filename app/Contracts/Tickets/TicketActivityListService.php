<?php

namespace App\Contracts\Tickets;

use Illuminate\Http\Request;

interface TicketActivityListService
{
    public function getTicketActivitiesByTicketHeaderId($ticketHeaderId);
}