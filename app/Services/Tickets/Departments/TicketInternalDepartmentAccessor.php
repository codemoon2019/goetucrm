<?php

namespace App\Services\Tickets\Departments;

use App\Models\UserType as Department;

class TicketInternalDepartmentAccessor extends TicketDepartmentAccessor
{
    public function getDepartments()
    {
        $userTypeIds = explode(",", $this->user->user_type_id);
        $columns = [
            'id',
            'description',
            'company_id',
        ];

        return Department::select($columns)
            ->with('partnerCompany:id,partner_id,company_name')
            ->with(['users' => function($query) {
                $columns = [
                    'id',
                    'image',
                    'first_name',
                    'last_name',
                    'user_type_id'
                ];
                
                $query
                    ->select($columns)
                    ->orderBy('first_name')
                    ->orderBy('last_name');
            }])
            ->isActive()
            ->whereIn('id', $userTypeIds)
            ->whereCompany($this->companyId)
            ->orderBy('description')
            ->get();
    }

}