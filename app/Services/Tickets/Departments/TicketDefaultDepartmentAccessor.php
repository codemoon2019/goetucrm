<?php

namespace App\Services\Tickets\Departments;

use App\Models\UserType as Department;

class TicketDefaultDepartmentAccessor extends TicketDepartmentAccessor
{
    public function getDepartments()
    {
        $columns = [
            'id',
            'description',
            'company_id',
            'head_id'
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
            ->isNonSystem()
            ->whereHas('partnerCompany', function($query) {
                $query->isActive();
            })
            ->whereHas('users', function ($query) {
                $query->isActive();
            })
            ->whereCompany($this->companyId)
            ->orderBy('description')
            ->get();
    }
}