<?php

namespace App\Services\Tickets\Departments;

use App\Models\User;

abstract class TicketDepartmentAccessor
{
    protected $user;
    protected $companyId;
    protected $departments;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->companyId = $user->company_id;
    }

    abstract protected function getDepartments();
}