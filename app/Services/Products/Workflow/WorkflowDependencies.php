<?php

namespace App\Services\Products\Workflow;

use App\Models\Product;
use App\Models\TicketPriority;
use App\Models\UserType;
use App\Models\User;

class WorkflowDependencies
{
    protected $product;

    public $departmentsNonGrouped;
    public $departments;
    public $priorities;
    public $users;

    public function __construct(Product $product)
    {
        $this->product = $product;

        $this->setDepartments();
        $this->setPriorities();
        $this->setUsers();
    }

    private function setDepartments()
    {
        $this->departmentsNonGrouped = $this->product->userTypes;
        $this->departments = $this->departmentsNonGrouped->groupBy('company_id');
    }

    private function setPriorities()
    {
        $this->priorities = TicketPriority::isActive()->get();
    }

    private function setUsers()
    {
        $departmentIds = $this->departments->pluck('id')->all();

        $this->users = User::isActive()
            ->whereUserTypeIn($departmentIds)
            ->get();
    }
}