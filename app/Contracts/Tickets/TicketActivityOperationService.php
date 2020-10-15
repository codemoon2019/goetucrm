<?php

namespace App\Contracts\Tickets;

use App\Models\TicketActivity;
use App\Models\TicketHeader;
use App\Models\User;
use Illuminate\Http\Request;

interface TicketActivityOperationService
{
    public function createTicketActivity(TicketHeader $ticketHeader, User $user) : TicketActivity; 
}