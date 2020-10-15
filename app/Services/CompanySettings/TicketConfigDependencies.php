<?php

namespace App\Services\CompanySettings;

use App\Models\Product;
use App\Models\TicketPriority;
use App\Models\UserType as Department;

class TicketConfigDependencies
{
    protected $companyId;
    public $ticketPriorities;

    public function __construct(int $companyId)
    {
        $this->companyId = $companyId;

        $this->setTicketPriorities();
    }

    public function setTicketPriorities()
    {
        $columns = ['id', 'code', 'description'];
        
        $this->ticketPriorities = TicketPriority::select($columns)
            ->isActive()
            ->get();
    }
}