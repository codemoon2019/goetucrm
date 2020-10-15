<?php

namespace App\Services\Tickets\Users;

use App\Models\UserType;

class TicketInternalUserAccessor extends TicketUserAccessor
{
    public function getUsers()
    {
        $userTypeIds = $this->user->userTypes()->pluck('user_types.id');
        $userTypes = UserType::with(['users' => function($query) {
                $query
                    ->with('department:id,description')
                    ->with('partnerCompany:id,partner_id,company_name')
                    ->isActive()
                    ->whereHas('partnerCompany', function($query) {
                        $query->isActive();
                    })
                    ->orderBy('first_name')
                    ->orderBy('last_name');
            }])
            ->find($userTypeIds);

        return $userTypes->reduce(function($carry, $item) {
            $usersProcessed = $item->users->map(function($user) use ($item) {
                $user->company_id = $item->company_id;

                return $user;
            });


            if ($carry == null)
                return $usersProcessed;

            return $carry->concat($usersProcessed);
        });
    }
}