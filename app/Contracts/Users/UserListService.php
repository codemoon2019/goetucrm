<?php

namespace App\Contracts\Users;

use Illuminate\Http\Request;

interface UserListService
{
    public function getMerchantUsersByCompany($companyId=null);
    public function getPartnerUsersByCompany($companyId=null);
    public function getRequesterUsersByCompany($companyId=null);
    public function getUsersByCompany($companyId=null);
    public function getUsersWithWorkflowAssignAccess($companyId=null);
    public function getUsersWhereUserTypeIn($userTypeIds);
}