<?php

namespace App\Services\Tickets\Users;

use App\Models\User;
use App\Services\Tickets\TicketUserClassification;
use Exception;

class TicketUserAccessorFactory
{
    public function make(User $user, int $ticketUserClassification)
    {
        switch ($ticketUserClassification) {
            case TicketUserClassification::SUPER_ADMIN:
            case TicketUserClassification::COMPANY;
            case TicketUserClassification::INTERNAL_USER_DEPARTMENT_HEAD:
                return new TicketDefaultUserAccessor($user);

            case TicketUserClassification::INTERNAL_USER:
                return new TicketInternalUserAccessor($user);

            case TicketUserClassification::PARTNER:
                return new TicketPartnerUserAccessor($user);

            case TicketUserClassification::MERCHANT:
                return new TicketMerchantUserAccessor($user);

            default:
                throw new Exception('Ticket User Classification not yet supported');
        }
    }
}