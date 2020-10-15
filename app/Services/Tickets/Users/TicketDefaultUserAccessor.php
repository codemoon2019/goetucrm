<?php

namespace App\Services\Tickets\Users;

use App\Models\Partner;

class TicketDefaultUserAccessor extends TicketUserAccessor
{
    public function getUsers()
    {
        $columns = [
            'id',
            'image',
            'first_name',
            'last_name',
            'user_type_id',
            'company_id',
        ];

        if ($this->companyId != -1) {
            return Partner::with(['users' => function($query) {
                    $query
                        ->with('department')
                        ->with('partnerCompany')
                        ->isActive()
                        ->whereHas('partnerCompany', function($query) {
                            $query->isActive();
                        })
                        ->orderBy('first_name')
                        ->orderBy('last_name')
                        ->get();
                }])
                ->find($this->companyId)
                ->users;
        }
        
        return Partner::with(['users' => function($query) {
            $query
                ->with('department')
                ->with('partnerCompany')
                ->isActive()
                ->whereHas('partnerCompany', function($query) {
                    $query->isActive();
                })
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();
        }])
        ->where('partner_type_id', 7)
        ->get()
        ->pluck('users')
        ->flatten();
    }
}