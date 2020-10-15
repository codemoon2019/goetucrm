<?php

namespace App\Services\Users;

use App\Contracts\Users\UserListService;
use App\Models\User;
use App\Models\UserType;
use App\Services\BaseServiceImpl;

class UserListServiceImpl extends BaseServiceImpl implements UserListService
{
    public function getMerchantUsersByCompany($companyId=null)
    {
        $users = User::isActive()
            ->with('department')
            ->with('partner.partnerCompany')
            ->with('partner.merchantBranches.partnerCompany')
            ->with('partner.merchantBranches.connectedUser')
            ->with('partnerCompany')
            ->whereHas('partnerCompany')
            ->whereCompany($companyId)
            ->whereHas('partner', function($query) {
                $query->where('partner_type_id', 3);
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->sortBy('partnerCompany.company_name', SORT_NATURAL|SORT_FLAG_CASE)
            ->groupBy('company_id');

        return $users;
    }

    public function getPartnerUsersByCompany($companyId=null)
    {
        $users = User::isActive()
            ->with('department', 'partnerCompany', 'partner.partner_type')
            ->whereCompany($companyId)
            ->whereIn('user_type_id', [4, 5, 6, 11, 13]) 
            ->whereHas('partner')
            ->whereHas('partner.partnerCompany')
            ->orderBy('user_type_id')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->sortBy('partnerCompany.company_name', SORT_NATURAL|SORT_FLAG_CASE)
            ->groupBy('company_id');

        return $users;
    }

    public function getRequesterUsersByCompany($companyId=null)
    {
        $users = User::isActive()
            ->whereHas('partnerCompany')
            ->with('partnerCompany', 'partner.partner_type')
            ->whereCompany($companyId)
            ->whereIn('user_type_id', [3, 4, 5, 6, 8, 11, 13]) 
            ->whereHas('partner')
            ->orderBy('user_type_id')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->sortBy('partnerCompany.company_name', SORT_NATURAL|SORT_FLAG_CASE)
            ->groupBy('company_id');

        return $users;
    }

    public function getDownlinePartnerUsersByCompany(User $user, $companyId=null)
    {
        switch($user->user_type_id) {
            case '6':
                $userTypeIds = [13];
                break;

            case '5':
                $userTypeIds = [13, 6];
                break;
              
            case '4': 
                $userTypeIds = [13, 6, 5];
                break;   

            default:
                $userTypeIds = [];
                break;
        }

        $users = User::isActive()
            ->with('partnerCompany', 'partner.partner_type')
            ->whereCompany($companyId)
            ->whereIn('user_type_id', $userTypeIds) 
            ->whereHas('partner')
            ->orderBy('user_type_id')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->sortBy('partnerCompany.company_name', SORT_NATURAL|SORT_FLAG_CASE)
            ->groupBy('company_id');

        return $users;
    }

    public function getUsersByCompany($companyId=null)
    {
        $users = User::isActive()
            ->with('department')
            ->with('partnerCompany')
            ->whereCompany($companyId)
            ->whereHas('partnerCompany')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->sortBy('partnerCompany.company_name')
            ->groupBy('company_id');

        return $users;
    }

    public function getUsersWithWorkflowAssignAccess($companyId=null)
    {
        $userTypeIds = UserType::isNonSystem()
            ->whereCompany($companyId)
            ->whereHas('resources', function($query) {
                $query->where('resource_id', 289);
            })
            ->pluck('id')
            ->toArray();

        return $this->getUsersWhereUserTypeIn($userTypeIds);
    }

    public function getUsersWhereUserTypeIn($userTypeIds)
    {
        $users = collect([]);
        foreach ($userTypeIds as $userTypeId) {
            $departmentUsers = User::isActive()
                ->whereUserType($userTypeId)
                ->get();

            $users = $users->merge($departmentUsers);
        }

        return $users->sortBy('first_name')->sortBy('last_name');
    }
}