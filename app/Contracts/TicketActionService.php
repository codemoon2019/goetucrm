<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface TicketActionService
{
    public function assignToMeTickets($ticketIds);
    public function assignTickets($ticketIds, $departmentId, $assigneeId);
    public function mergeTickets($ticketIds);
    public function deleteTickets($ticketIds);
}