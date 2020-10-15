<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface DepartmentService
{
    public function createDefaultDepartments($companyId);
    public function store($data);
    public function storeAccessList($departmentId, $resourceIds);
}