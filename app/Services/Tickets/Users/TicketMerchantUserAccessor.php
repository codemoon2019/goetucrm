<?php

namespace App\Services\Tickets\Users;

use App\Models\User;

class TicketMerchantUserAccessor extends TicketUserAccessor
{
    public function getUsers()
    {
        $user = $this->user->partner->upline->connectedUser;
        $user->load('department:id,description');
        $user->load('partnerCompany:id,partner_id,company_name');

        return collect([$user]);
    }
}