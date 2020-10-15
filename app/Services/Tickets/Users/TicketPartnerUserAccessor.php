<?php

namespace App\Services\Tickets\Users;

use App\Models\User;

class TicketPartnerUserAccessor extends TicketUserAccessor
{
    public function getUsers()
    {
        $partnerIds = $this->user->partner->downlines->pluck('id');
        $partnerIds[] = $this->user->partner->upline->id;

        $columns = [
            'id',
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
            ->where('is_original_partner', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }
}