<?php

use Illuminate\Database\Seeder;

class AddReportExportAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('resources')->insert(array (
            0 => 
                array (
                    'id' => 297,
                    'resource' => '',
                    'description' => 'ACH Transaction Export',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 23,
                    'resource_group_access_id' => 160,
                    'update_by' => 'admin',
                    'created_at' => '2019-01-14 11:00:00',
                    'updated_at' => '2019-01-14 11:00:00',
                ),

            1 => 
                array (
                    'id' => 298,
                    'resource' => '',
                    'description' => 'Monthly Sales Export',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 23,
                    'resource_group_access_id' => 161,
                    'update_by' => 'admin',
                    'created_at' => '2019-01-14 11:00:00',
                    'updated_at' => '2019-01-14 11:00:00',
                ),

            2 => 
                array (
                    'id' => 299,
                    'resource' => '',
                    'description' => 'Commission Export',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 23,
                    'resource_group_access_id' => 162,
                    'update_by' => 'admin',
                    'created_at' => '2019-01-14 11:00:00',
                    'updated_at' => '2019-01-14 11:00:00',
                ),

        ));
        
        \DB::table('resource_group_accesses')->insert(array (
            0 => 
                array (
                    'id' => 160,
                    'resource_group_id' => 23,
                    'name' => 'ACH Transaction Export',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-01-14 11:00:00',
                    'updated_at' => '2019-01-14 11:00:00',
                ),

            1 => 
                array (
                    'id' => 161,
                    'resource_group_id' => 23,
                    'name' => 'Monthly Sales Export',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-01-14 11:00:00',
                    'updated_at' => '2019-01-14 11:00:00',
                ),

            2 => 
                array (
                    'id' => 162,
                    'resource_group_id' => 23,
                    'name' => 'Commission Export',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-01-14 11:00:00',
                    'updated_at' => '2019-01-14 11:00:00',
                ),


        ));

    }
}
