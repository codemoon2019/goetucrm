<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface TicketListService
{
    public function listInternalTickets($filterCode, $statusCode, $departmentIds, 
        $priorityCode, $companyId, $requesterId);

    public function listPartnerOrMerchantTickets($statusCode, $priorityCode);

    public function formatTicketsForDatatable($ticketHeaders);
}