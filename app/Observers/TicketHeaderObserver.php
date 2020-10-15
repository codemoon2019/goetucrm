<?php

namespace App\Observers;

use App\Models\TicketHeader;
use App\Models\User;
use App\Contracts\Tickets\TicketActivityOperationService;

class TicketHeaderObserver
{
    protected $taoService; /** tao = ticketActivityOperation */

    public function __construct(TicketActivityOperationService $taoService)
    {
        $this->taoService = $taoService;
    }

    public function updating(TicketHeader $ticketHeader)
    {
        if (auth()->user()) {
            $this->taoService->createTicketActivity($ticketHeader, User::find(auth()->user()->id));
        }
    }
}

