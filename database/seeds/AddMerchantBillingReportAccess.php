<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Resource;
use App\Models\ResourceGroupAccess;

class AddMerchantBillingReportAccess extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function() {
            $resourceGroupAccess = ResourceGroupAccess::create([
                'resource_group_id' => 23,
                'name' => 'Billing Status Report',
                'create_by' => 'Seeder',
                'status' => 'A',
            ]);
    
            $resource = Resource::create([
                'resource' => 'Billing Status Report',
                'description' =>  'View Merchant and Branch Billing Status Report',
                'create_by' => 'Seeder',
                'deleted' => 0,
                'resource_group_id' => 23,
                'resource_group_access_id' => $resourceGroupAccess->id,
            ]);
        });
    }
}
