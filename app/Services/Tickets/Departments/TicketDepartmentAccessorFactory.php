<?php

namespace App\Services\Tickets\Departments;

use App\Models\User;
use App\Services\Tickets\TicketUserClassification;
use Exception;

class TicketDepartmentAccessorFactory
{
    public function make(User $user, int $ticketUserClassification)
    {
        switch ($ticketUserClassification) {
            case TicketUserClassification::SUPER_ADMIN:
            case TicketUserClassification::COMPANY;
            case TicketUserClassification::INTERNAL_USER_DEPARTMENT_HEAD:
                return new TicketDefaultDepartmentAccessor($user);

            case TicketUserClassification::INTERNAL_USER:
                return new TicketInternalDepartmentAccessor($user);

            default:
                throw new Exception('Ticket User Classification not yet supported');
        }
    }
}