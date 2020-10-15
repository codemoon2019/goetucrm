<?php

namespace App\Contracts\Departments;

use Illuminate\Http\Request;

interface DepartmentListService
{
    public function getInternalDepartmentsByCompanyNoGrouping($userTypeIds, $companyId=null);
    public function getInternalDepartmentsByCompany($userTypeIds, $companyId=null);
}