<?php

namespace App\Services\Tickets\Users;

use App\Models\User;

abstract class TicketUserAccessor
{
    protected $user;
    protected $companyId;

    protected $users;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->companyId = $user->company_id;
    }

    abstract protected function getUsers();
}