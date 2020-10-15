<?php

use Illuminate\Database\Seeder;

class AddEditPartnerStatusesToResourcesSeeder extends Seeder
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
                    'id' => 300,
                    'resource' => '',
                    'description' => 'Edit Partner Status',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 2,
                    'resource_group_access_id' => 163,
                    'update_by' => 'admin',
                    'created_at' => '2019-01-14 11:00:00',
                    'updated_at' => '2019-01-14 11:00:00',
                ),
            
            1 => 
                array (
                    'id' => 301,
                    'resource' => '',
                    'description' => 'Edit Partner Status',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 8,
                    'resource_group_access_id' => 164,
                    'update_by' => 'admin',
                    'created_at' => '2019-01-14 11:00:00',
                    'updated_at' => '2019-01-14 11:00:00',
                ),
            
            2 => 
                array (
                    'id' => 302,
                    'resource' => '',
                    'description' => 'Edit Partner Status',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 13,
                    'resource_group_access_id' => 165,
                    'update_by' => 'admin',
                    'created_at' => '2019-01-14 11:00:00',
                    'updated_at' => '2019-01-14 11:00:00',
                ),

            3 => 
                array (
                    'id' => 303,
                    'resource' => '',
                    'description' => 'Edit Partner Status',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 14,
                    'resource_group_access_id' => 166,
                    'update_by' => 'admin',
                    'created_at' => '2019-01-14 11:00:00',
                    'updated_at' => '2019-01-14 11:00:00',
                ),

            4 => 
                array (
                    'id' => 304,
                    'resource' => '',
                    'description' => 'Edit Partner Status',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 15,
                    'resource_group_access_id' => 167,
                    'update_by' => 'admin',
                    'created_at' => '2019-01-14 11:00:00',
                    'updated_at' => '2019-01-14 11:00:00',
                ),
        ));

        \DB::table('resource_group_accesses')->insert(array (
            149 => 
                array (
                    'id' => 163,
                    'resource_group_id' => 2, // Agent
                    'name' => 'Edit Partner Status',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-02-06 10:05:00',
                    'updated_at' => '2019-02-06 10:05:00',
                ),
            
            150 => 
                array (
                    'id' => 164,
                    'resource_group_id' => 8, // Company
                    'name' => 'Edit Partner Status',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-02-06 10:05:00',
                    'updated_at' => '2019-02-06 10:05:00',
                ),
            
            151 => 
                array (
                    'id' => 165,
                    'resource_group_id' => 13, // SubAgent
                    'name' => 'Edit Partner Status',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-02-06 10:05:00',
                    'updated_at' => '2019-02-06 10:05:00',
                ), 
            
            152 => 
                array (
                    'id' => 166,
                    'resource_group_id' => 14, // ISO
                    'name' => 'Edit Partner Status',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-02-06 10:05:00',
                    'updated_at' => '2019-02-06 10:05:00',
                ), 
            
            153 => 
                array (
                    'id' => 167,
                    'resource_group_id' => 15, // SubISO
                    'name' => 'Edit Partner Status',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-02-06 10:05:00',
                    'updated_at' => '2019-02-06 10:05:00',
                ), 

        ));
    }
}
