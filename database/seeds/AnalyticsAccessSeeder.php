<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnalyticsAccessSeeder extends Seeder
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
                'id' => 182,
                'resource_group_id' => 1,
                'name' => 'View Analytics',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'created_at' => '2017-08-11 00:30:04',
                'updated_at' => '2018-06-07 01:36:20',
            ],
        ];

        $resourceData = [
            [
                'id' => 318,
                'resource' => 'admin/analytics',
                'description' => 'View',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'deleted' => 0,
                'resource_group_id' => 1,
                'resource_group_access_id' => 182,
                'created_at' => '2018-10-02 11:00:00',
                'updated_at' => '2018-10-02 11:00:00',
            ],
        ];

        DB::table('resource_group_accesses')->insert($resourceGroupAccessData);
        DB::table('resources')->insert($resourceData);
    }
}
