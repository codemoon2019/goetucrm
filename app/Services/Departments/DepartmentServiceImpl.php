<?php

namespace App\Services\Departments;

use App\Contracts\DepartmentService;
use App\Models\AccessControlList;
use App\Models\UserType;
use App\Models\UserTemplate;
use App\Services\BaseServiceImpl;
use Illuminate\Support\Facades\DB;


class DepartmentServiceImpl extends BaseServiceImpl implements DepartmentService
{
    public function createDefaultDepartments($companyId)
    {
        $username = auth()->user()->username;

        $constantData = [
            'create_by' => auth()->user()->username,
            'update_by' => auth()->user()->username,
            'status' => 'A',
            'company_id' => $companyId,
            'parent_id' => -1,
            'head_id' => -1,
        ];

        DB::beginTransaction();

        try {
            $accountingDepartment =  $this->store(array_merge($constantData, [
                'description' => 'Accounting',
                'display_name' => 'Accounting'
            ]));
            

            $resources = AccessControlList::getResourcesViaResourceGroupAccess([
                '97', '8', '86', '68', '29', '53', '16', '17', '76', '66', 
                '77', '135', '25', '31', '45', '39', '41', '49', '57', '79', 
                '63', '61'
            ]);

            $this->storeAccessList($accountingDepartment->id, $resources);


            $operationsDepartment = $this->store(array_merge($constantData, [
                'description' => 'Operations', 
                'display_name' => 'Operations'
            ]));

            $resources = AccessControlList::getResourcesViaResourceGroupAccess([
                '17', '18', '76', '117', '108', '19', '123', '124', '121', '66', 
                '118', '120', '77', '21', '125', '122', '135', '25', '39', '41', 
                '78', '85', '80', '81', '79', '112', '630'
            ]);

            $this->storeAccessList($operationsDepartment->id, $resources);


            $supportDepartment = $this->store(array_merge($constantData, [
                'description' => 'Support',
                'display_name' => 'Support'
            ]));

            $resources = AccessControlList::getResourcesViaResourceGroupAccess([
                '4', '8', '86', '29', '53', '16', '21', '135', '25', '35', '37', 
                '45', '39', '41', '49', '57', '78', '85', '80', '81', '83', '82', 
                '141', '79', '63', '61'
            ]);
            
            $this->storeAccessList($supportDepartment->id, $resources);

    
            $salesDepartment = $this->store(array_merge($constantData, [
                'description' => 'Sales and Marketing',
                'display_name' => 'Sales and Marketing  '
            ]));

            $resources = AccessControlList::getResourcesViaResourceGroupAccess([
                '8', '29', '53', '13', '64', '14', '16', '21', '135', '25', 
                '37', '42', '65', '43', '45', '39', '41', '49', '57', '78', 
                '85', '81', '83', '82', '79', '63', '61'
            ]);

            $this->storeAccessList($salesDepartment->id, $resources);

            DB::commit();
            return $supportDepartment->id;
        } catch (Exception $ex) {
            DB::rollback();
            return false;
        }
    }

    public function store($data)
    {
        return UserType::create($data);
    }

    public function storeAccessList($departmentId, $resources)
    {
        $data = [];
        foreach ($resources as $resource) {
            $data[] = [
                'user_type_id' => $departmentId, 
                'resource_id' => $resource->id
            ];
        }

        return UserTemplate::insert($data);
    }
}