<?php

namespace App\Services\Users;

use App\Models\Access;
use App\Models\UserType as Department;
use App\Models\User;

class UserClassification
{
    protected $user;

    public $isAdmin = false;
    public $isCompany = false;
    public $isInternal = false;
    public $isInternalDepartmentHead = false;
    public $isPartner = false;
    public $isMerchant = false;
    public $isBranch = false;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->setClassification();
    }

    protected function hasSuperAdminAccess()
    {
        return Access::hasPageAccess('admin', 'super admin access', true);
    }

    protected function isDepartmentHead()
    {
        return (boolean) Department::where('head_id', $this->user->id)->count();
    }

    public function setClassification()
    {
        if ($this->hasSuperAdminAccess()) {
            $this->isAdmin = true;
            return;
        }

        if ($this->user->is_original_partner == false) {
            if ($this->isDepartmentHead($this->user)) {
                $this->isInternalDepartmentHead = true;
            } else {
                $this->isInternal = true;
            }

            return;
        } 
        
        if ($this->user->partner->partner_type_id == 3) {
            $this->isMerchant = true;
            return;
        } else if ($this->user->partner->partner_type_id == 9) {
            $this->isBranch = true;
            return;
        } else if ($this->user->partner->partner_type_id == 7) {
            $this->isCompany = true;
            return;
        }
            
        $this->isPartner = true;
    }
}