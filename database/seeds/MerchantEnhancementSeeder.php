<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MerchantEnhancementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $resourceGroupAccessData = [
            [
                'id' => 172,
                'resource_group_id' => 6,
                'name' => 'View Merchant Boarding',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'status' => 'A',
                'created_at' => '2017-08-11 00:30:04',
                'updated_at' => '2018-06-07 01:36:20',
            ],
            [
                'id' => 173,
                'resource_group_id' => 6,
                'name' => 'View Merchant Approval',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'status' => 'A',
                'created_at' => '2017-08-11 00:30:04',
                'updated_at' => '2018-06-07 01:36:20',
            ],
            [
                'id' => 174,
                'resource_group_id' => 6,
                'name' => 'Decline Merchant',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'status' => 'A',
                'created_at' => '2017-08-11 00:30:04',
                'updated_at' => '2018-06-07 01:36:20',
            ],
        ];

        $resourceData = [
            [
                'id' => 309,
                'resource' => '/merchants/board_merchant',
                'description' => 'View Merchant Boarding',
                'create_by' => 'Seeder',
                'deleted' => 0,
                'resource_group_id' => 6,
                'resource_group_access_id' => 172,
                'update_by' => 'admin',
                'created_at' => '2018-10-02 11:00:00',
                'updated_at' => '2018-10-02 11:00:00',
            ],
            [
                'id' => 310,
                'resource' => '/merchants/approve_merchant',
                'description' => 'View Merchant Approval',
                'create_by' => 'Seeder',
                'deleted' => 0,
                'resource_group_id' => 6,
                'resource_group_access_id' => 173,
                'update_by' => 'admin',
                'created_at' => '2018-10-02 11:00:00',
                'updated_at' => '2018-10-02 11:00:00',
            ],
            [
                'id' => 311,
                'resource' => '',
                'description' => 'Decline Merchant',
                'create_by' => 'Seeder',
                'deleted' => 0,
                'resource_group_id' => 6,
                'resource_group_access_id' => 174,
                'update_by' => 'admin',
                'created_at' => '2018-10-02 11:00:00',
                'updated_at' => '2018-10-02 11:00:00',
            ]
        ];

        DB::table('resource_group_accesses')->insert($resourceGroupAccessData);
        DB::table('resources')->insert($resourceData);
    }
}
