<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Resource;
use App\Models\ResourceGroupAccess;

class AddActiveSwithToAccessSeeder extends Seeder
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
                'resource_group_id' => 16,
                'name' => 'Activate Switch',
                'create_by' => 'Seeder',
                'status' => 'A',
            ]);
    
            $resource = Resource::create([
                'resource' => 'Activate Switch',
                'description' =>  'Activate and Deactivate User',
                'create_by' => 'Seeder',
                'deleted' => 0,
                'resource_group_id' => 16,
                'resource_group_access_id' => $resourceGroupAccess->id,
            ]);
        });
    }
}
