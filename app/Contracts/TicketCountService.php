<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface TicketCountService
{
    public function countTickets();
}