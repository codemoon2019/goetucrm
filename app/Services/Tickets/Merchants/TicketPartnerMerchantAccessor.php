<?php

namespace App\Services\Tickets\Merchants;

use App\Models\User;

class TicketPartnerMerchantAccessor extends TicketMerchantAccessor
{
    public function getMerchants()
    {
        $partnerIds = $this->user->partner->downlines->pluck('id');

        $columns = [
            'id',
            'image',
            'first_name',
            'last_name',
            'reference_id',
            'user_type_id',
            'company_id',
        ];

        return User::select($columns)
            ->with('department:id,description')
            ->with('partnerCompany:id,partner_id,company_name')
            ->with('partner.partnerCompany:id,partner_id,company_name')
            ->with(['partner.merchantBranches.connectedUser' => function($query) {
                $query->select(['id', 'image', 'first_name', 'last_name'])
                    ->isActive()
                    ->orderBy('first_name')
                    ->orderBy('last_name');
            }])
            ->isActive()
            ->whereIn('reference_id', $partnerIds)
            ->whereHas('partner', function($query) {
                $query->where('partner_type_id', 3);
            })
            ->where('is_original_partner', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }
}