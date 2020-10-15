<?php

namespace App\Services\Tickets;

use App\Models\Access;
use App\Models\UserType as Department;
use App\Models\User;

class TicketUserClassification
{
    protected $user;
    protected $companyId;

    public $isInternal = false;
    public $isPartner = false;
    public $isMerchant = false;

    /**
     * 0 to 3 are considered internal users
     */
    const SUPER_ADMIN = 0;
    const COMPANY = 1;
    const INTERNAL_USER_DEPARTMENT_HEAD = 2;
    const INTERNAL_USER = 3;
    const PARTNER = 4;
    const MERCHANT = 5;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->companyId = $user->company_id;
    }

    protected function isDepartmentHead()
    {
        return (boolean) Department::where('head_id', $this->user->id)
            ->whereCompany($this->companyId)
            ->count();
    }

    public function getClassification()
    {
        if (Access::hasPageAccess('admin', 'super admin access', true)) {
            $this->isInternal = true;
            return self::SUPER_ADMIN;
        }

        if ($this->user->is_original_partner == false) {
            $this->isInternal = true;

            if ($this->isDepartmentHead()) {
                return self::INTERNAL_USER_DEPARTMENT_HEAD;
            } else {
                return self::INTERNAL_USER;
            }
        } else {
            if ($this->user->partner->partner_type_id == 7) {
                $this->isInternal = true;
                return self::COMPANY;
            }
        }

        if ($this->user->partner->partner_type_id == 3) {
            $this->isMerchant = true;
            return self::MERCHANT;
        }
            
        $this->isPartner = true;
        return self::PARTNER;
    }
}