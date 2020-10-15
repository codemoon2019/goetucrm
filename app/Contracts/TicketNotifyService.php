<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface TicketNotifyService
{
    public function notifyOnAction($ticketHeaders, $action);
}