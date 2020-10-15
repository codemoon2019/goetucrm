<?php

namespace App\Services\Tickets\Partners;

use App\Models\User;
use App\Services\Tickets\TicketUserClassification;
use Exception;

class TicketPartnerAccessorFactory
{
    public function make(User $user, int $ticketUserClassification)
    {
        switch ($ticketUserClassification) {
            case TicketUserClassification::SUPER_ADMIN:
            case TicketUserClassification::COMPANY;
            case TicketUserClassification::INTERNAL_USER_DEPARTMENT_HEAD:
            case TicketUserClassification::INTERNAL_USER:
                return new TicketDefaultPartnerAccessor($user);

            case TicketUserClassification::PARTNER:
                return new TicketPartnerPartnerAccessor($user);

            default:
                throw new Exception('Ticket User Classification not yet supported');
        }
    }
}