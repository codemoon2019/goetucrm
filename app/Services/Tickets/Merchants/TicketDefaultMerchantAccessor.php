<?php

namespace App\Services\Tickets\Merchants;

use App\Models\User;

class TicketDefaultMerchantAccessor extends TicketMerchantAccessor
{
    public function getMerchants()
    {
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
            ->with('partner:id,parent_id,partner_type_id')
            ->with('partner.partnerCompany:id,partner_id,company_name')
            ->with('partner.merchantBranches:id,parent_id,reference_id')
            ->with(['partner.merchantBranches.connectedUser' => function($query) {
                $query->select(['id', 'image', 'first_name', 'last_name'])
                    ->isActive()
                    ->orderBy('first_name')
                    ->orderBy('last_name');
            }])
            ->whereHas('partnerCompany')
            ->whereHas('partner', function($query) {
                $query
                    ->isActive()
                    ->where('partner_type_id', 3);
            })
            ->whereCompany($this->companyId)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }
}