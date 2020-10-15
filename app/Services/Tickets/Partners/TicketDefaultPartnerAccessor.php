<?php

namespace App\Services\Tickets\Partners;

use App\Models\User;

class TicketDefaultPartnerAccessor extends TicketPartnerAccessor
{
    public function getPartners()
    {
        $columns = [
            'id',
            'image',
            'username',
            'first_name',
            'last_name',
            'reference_id',
            'user_type_id',
            'company_id',
        ];

        return User::select($columns)
            ->with('department:id,description')
            ->with('partnerCompany:id,partner_id,company_name')
            ->isActive()
            ->whereHas('partnerCompany', function($query) {
                $query->isActive();
            })
            ->whereCompany($this->companyId)
            ->whereIn('user_type_id', [4, 5, 6, 11, 13])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }
}