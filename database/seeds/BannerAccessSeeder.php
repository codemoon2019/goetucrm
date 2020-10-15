<?php

use App\Models\ResourceGroup;
use Illuminate\Database\Seeder;

class BannerAccessSeeder extends Seeder
{
    public function run()
    {
        $resourceGroup = ResourceGroup::find(24);
        if (optional($resourceGroup)->name == 'Banner') {
            $resourceGroup->name = 'Announcement';
            $resourceGroup->save();
        } else {
            $resourceGroupData = [
                [
                    'id' => 24,
                    'name' => 'Banner',
                    'partner_type_access' => null,
                    'status' => 'A',
                    'create_by' => 'Seeder',
                    'update_by' => null,
                    'created_at' => '2017-08-11 00:30:04',
                    'updated_at' => '2018-06-07 01:36:20',
                ]
            ];

            $resourceGroupAccessData = [
                [
                    'id' => 175,
                    'resource_group_id' => 24,
                    'name' => 'View',
                    'create_by' => 'Seeder',
                    'update_by' => 'Seeder',
                    'status' => 'A',
                    'created_at' => '2017-08-11 00:30:04',
                    'updated_at' => '2018-06-07 01:36:20',
                ],
                [
                    'id' => 176,
                    'resource_group_id' => 24,
                    'name' => 'Create',
                    'create_by' => 'Seeder',
                    'update_by' => 'Seeder',
                    'status' => 'A',
                    'created_at' => '2017-08-11 00:30:04',
                    'updated_at' => '2018-06-07 01:36:20',
                ],
                [
                    'id' => 177,
                    'resource_group_id' => 24,
                    'name' => 'Edit',
                    'create_by' => 'Seeder',
                    'update_by' => 'Seeder',
                    'status' => 'A',
                    'created_at' => '2017-08-11 00:30:04',
                    'updated_at' => '2018-06-07 01:36:20',
                ],
                [
                    'id' => 178,
                    'resource_group_id' => 24,
                    'name' => 'Delete',
                    'create_by' => 'Seeder',
                    'update_by' => 'Seeder',
                    'status' => 'A',
                    'created_at' => '2017-08-11 00:30:04',
                    'updated_at' => '2018-06-07 01:36:20',
                ],
            ];

            $resourceData = [
                [
                    'id' => 312,
                    'resource' => 'admin/banners',
                    'description' => 'View',
                    'create_by' => 'Seeder',
                    'update_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 24,
                    'resource_group_access_id' => 175,
                    'created_at' => '2018-10-02 11:00:00',
                    'updated_at' => '2018-10-02 11:00:00',
                ],
                [
                    'id' => 313,
                    'resource' => 'admin/banners/create',
                    'description' => 'Create',
                    'create_by' => 'Seeder',
                    'update_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 24,
                    'resource_group_access_id' => 176,
                    'created_at' => '2018-10-02 11:00:00',
                    'updated_at' => '2018-10-02 11:00:00',
                ],
                [
                    'id' => 314,
                    'resource' => 'admin/banners/edit',
                    'description' => 'Edit',
                    'create_by' => 'Seeder',
                    'update_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 24,
                    'resource_group_access_id' => 177,
                    'created_at' => '2018-10-02 11:00:00',
                    'updated_at' => '2018-10-02 11:00:00',
                ]
            ];

            DB::table('resource_groups')->insert($resourceGroupData);
            DB::table('resource_group_accesses')->insert($resourceGroupAccessData);
            DB::table('resources')->insert($resourceData);
        }
    }
}
