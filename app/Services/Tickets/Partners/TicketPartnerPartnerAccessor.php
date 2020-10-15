<?php

namespace App\Services\Tickets\Partners;

use App\Models\User;

class TicketPartnerPartnerAccessor extends TicketPartnerAccessor
{
    public function getPartners()
    {
        $partnerIds = $this->user->partner->downlines->pluck('id');
        $partnerIds[] = $this->user->partner->id;
        $columns = [
            'id',
            'username',
            'image',
            'first_name',
            'last_name',
            'user_type_id',
            'company_id',
        ];

        return User::select($columns)
            ->with('department:id,description')
            ->with('partnerCompany:id,partner_id,company_name')
            ->isActive()
            ->whereIn('reference_id', $partnerIds)
            ->whereIn('user_type_id', [4, 5, 6, 11, 13])
            ->where('is_original_partner', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }
}