<?php

namespace App\Services\Tickets\Merchants;

use App\Models\User;

abstract class TicketMerchantAccessor
{
    protected $user;
    protected $companyId;
    protected $departments;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->companyId = $user->company_id;
    }

    abstract protected function getMerchants();
}