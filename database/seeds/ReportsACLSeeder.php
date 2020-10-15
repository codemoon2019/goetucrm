<?php

use Illuminate\Database\Seeder;

class ReportsACLSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        \DB::table('resource_groups')->insert(array (
            0 => 
            array (
                'id' => 23,
                'name' => 'Reports',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-11-26 01:36:20',
                'updated_at' => '2018-11-26 01:36:20',
            )
        ));

        \DB::table('resources')->insert(array (
            0 => 
            array (
                'id' => 290,
                'resource' => '',
                'description' => 'ACH Transaction Report',
                'create_by' => 'Seeder',
                'deleted' => 0,
                'resource_group_id' => 23,
                'resource_group_access_id' => 153,
                'update_by' => 'admin',
                'created_at' => '2018-11-26 11:00:00',
                'updated_at' => '2018-11-26 11:00:00',
            ),

            1 => 
            array (
                'id' => 291,
                'resource' => '',
                'description' => 'Monthly Sales Report',
                'create_by' => 'Seeder',
                'deleted' => 0,
                'resource_group_id' => 23,
                'resource_group_access_id' => 154,
                'update_by' => 'admin',
                'created_at' => '2018-11-26 11:00:00',
                'updated_at' => '2018-11-26 11:00:00',
            ),

            2 => 
            array (
                'id' => 292,
                'resource' => '',
                'description' => 'Commission Report',
                'create_by' => 'Seeder',
                'deleted' => 0,
                'resource_group_id' => 23,
                'resource_group_access_id' => 155,
                'update_by' => 'admin',
                'created_at' => '2018-11-26 11:00:00',
                'updated_at' => '2018-11-26 11:00:00',
            ),


        ));


        \DB::table('resource_group_accesses')->insert(array (
            0 => 
            array (
                'id' => 153,
                'resource_group_id' => 23,
                'name' => 'ACH Transaction Report',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'status' => 'A',
                'created_at' => '2018-11-26 00:30:04',
                'updated_at' => '2018-11-26 01:36:20',
            ),

            1 => 
            array (
                'id' => 154,
                'resource_group_id' => 23,
                'name' => 'Monthly Sales Report',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'status' => 'A',
                'created_at' => '2018-11-26 00:30:04',
                'updated_at' => '2018-11-26 01:36:20',
            ),

            2 => 
            array (
                'id' => 155,
                'resource_group_id' => 23,
                'name' => 'Commission Report',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'status' => 'A',
                'created_at' => '2018-11-26 00:30:04',
                'updated_at' => '2018-11-26 01:36:20',
            ),


        ));


    }
}
