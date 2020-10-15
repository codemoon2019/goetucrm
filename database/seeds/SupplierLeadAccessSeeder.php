<?php

use App\Models\Resource;
use App\Models\ResourceGroup;
use App\Models\ResourceGroupAccess;
use Illuminate\Database\Seeder;

class SupplierLeadAccessSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function() {
            $resourceGroup = ResourceGroup::create([
                'name' => 'Supplier Leads',
                'partner_type_access' => null,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => null,
            ]);

            $rga1 = ResourceGroupAccess::create([
                'name' => 'View',
                'description' => 'View Supplier Leads',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'resource_group_id' => $resourceGroup->id
            ]);
    
            $resource1 = Resource::create([
                'resource' => 'supplier-leads/',
                'description' => 'View',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'resource_group_id' => $resourceGroup->id,
                'resource_group_access_id' => $rga1->id,
            ]);
    
            $rga2 = ResourceGroupAccess::create([
                'name' => 'Create',
                'description' => 'Create Supplier Lead',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'resource_group_id' => $resourceGroup->id,
            ]);

            $resource1 = Resource::create([
                'resource' => "supplier-leads/create",
                'description' => 'Create',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'resource_group_id' => $resourceGroup->id,
                'resource_group_access_id' => $rga2->id,
            ]);
        });
    }
}
