<?php

use Illuminate\Database\Seeder;
use App\Models\Resource;
use App\Models\ResourceGroupAccess;

class UserTerminationAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rga = ResourceGroupAccess::find(183);
        if ($rga != null && $rga->name == 'Terminate') {
            $rga->delete();
        }

        $rga = ResourceGroupAccess::create([
            'resource_group_id' => 16,
            'name' => 'Terminate',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder',
            'status' => 'A',
        ]);

        $resource = Resource::create([
            'resource' => '',
            'description' => 'Terminate',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder',
            'deleted' => 0,
            'resource_group_id' => 16,
            'resource_group_access_id' => $rga->id,
        ]);
    }
}
