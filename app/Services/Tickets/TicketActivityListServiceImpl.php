<?php

namespace App\Services\Tickets;

use App\Contracts\Tickets\TicketActivityListService;
use App\Models\TicketActivity;
use App\Services\BaseServiceImpl;

class TicketActivityListServiceImpl extends BaseServiceImpl implements TicketActivityListService
{
    public function getTicketActivitiesByTicketHeaderId($ticketHeaderId)
    {
        return TicketActivity::with('createdBy')
            ->where('ticket_header_id', $ticketHeaderId)
            ->orderBy('created_at', 'DESC')
            ->get();
    }
}