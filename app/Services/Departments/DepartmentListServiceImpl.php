<?php

namespace App\Services\Departments;

use App\Contracts\Departments\DepartmentListService;
use App\Models\Access;
use App\Models\UserType;
use App\Models\UserTemplate;
use App\Services\BaseServiceImpl;

class DepartmentListServiceImpl extends BaseServiceImpl implements DepartmentListService
{
    public function getInternalDepartmentsByCompanyNoGrouping($userTypeIdString, $companyId=null)
    {
        if (Access::hasPageAccess('ticketing', 'assign', true)) {
            $departments = UserType::isActive()
                ->isNonSystem()
                ->with(['users' => function($query) {
                    $query->orderBy('first_name')->orderBy('last_name');
                }])
                ->with('partnerCompany')
                ->whereHas('partnerCompany')
                ->whereCompany($companyId)
                ->orderBy('description')
                ->get();
        } else {
            $userTypeIds = explode(',', $userTypeIdString);
            $userType = UserType::find($userTypeIds[0]);
            
            $departments = UserType::isActive()
                ->isNonSystem()
                ->with(['users' => function($query) {
                    $query->whereHas('partnerCompany')->orderBy('first_name')->orderBy('last_name');
                }])
                ->with('partnerCompany')
                ->whereHas('partnerCompany')
                ->where('id', $userType->id)
                ->whereCompany($companyId)
                ->orderBy('description')
                ->get(); 

            $userTemplates = UserTemplate::withAssignAccess()->get();
            $userTypeIdArray = [];
            foreach ($userTemplates as $userTemplate) {
                $userTypeIdArray[] = $userTemplate->user_type_id;
            }

            $departmentsWithAssignAccess = UserType::isActive()
                ->isNonSystem()
                ->with('users')
                ->with('partnerCompany')
                ->whereHas('partnerCompany')
                ->where('status', 'A')
                ->whereCompany($companyId)
                ->whereIn('id', $userTypeIdArray)   
                ->orderBy('description')
                ->get();

            $departments = $departments->merge($departmentsWithAssignAccess);
        }

        return $departments;
    }

    public function getInternalDepartmentsByCompany($userTypeIdString, $companyId=null)
    {
        return $this->getInternalDepartmentsByCompanyNoGrouping($userTypeIdString, $companyId)
            ->sortBy('partnerCompany.company_name', SORT_NATURAL|SORT_FLAG_CASE)
            ->groupBy('company_id');
    }

}