<?php

use App\Models\Resource;
use App\Models\ResourceGroupAccess;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserActivitiesReportAccessSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function() {
            $rga1 = ResourceGroupAccess::create([
                'name' => 'User Activities Report',
                'description' => 'User Activities Report',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'resource_group_id' => 23
            ]);
    
            $resource1 = Resource::create([
                'resource' => 'reports/user-activities',
                'description' => 'User Activities Report',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'resource_group_id' => 23,
                'resource_group_access_id' => $rga1->id,
            ]);
    
            $rga2 = ResourceGroupAccess::create([
                'name' => 'U.A. Report (Excel)',
                'description' => 'User Activities Report (Excel)',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'resource_group_id' => 23
            ]);
    
            $resource2 = Resource::create([
                'resource' => 'reports/user-activities',
                'description' => 'U.A. Report (Excel)',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'resource_group_id' => 23,
                'resource_group_access_id' => $rga2->id,
            ]);
        });
    }
}
