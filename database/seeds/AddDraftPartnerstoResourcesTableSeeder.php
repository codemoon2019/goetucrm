<?php

use Illuminate\Database\Seeder;

class AddDraftPartnerstoResourcesTableSeeder extends Seeder
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
                    'id' => 315,
                    'resource' => 'drafts',
                    'description' => 'Draft Applicants List',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 25,
                    'resource_group_access_id' => 179,
                    'update_by' => 'admin',
                    'created_at' => '2019-03-20 11:00:20',
                    'updated_at' => '2019-03-20 11:00:20',
                ),
            1 => 
                array (
                    'id' => 316,
                    'resource' => 'drafts/edit',
                    'description' => 'Edit',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 25,
                    'resource_group_access_id' => 180,
                    'update_by' => 'admin',
                    'created_at' => '2019-03-20 11:00:20',
                    'updated_at' => '2019-03-20 11:00:20',
                ),
        )); 

        \DB::table('resource_groups')->insert(array (
            0 => 
                array (
                    'id' => 25,
                    'name' => 'Draft Applicants',
                    'partner_type_access' => NULL,
                    'status' => 'A',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'created_at' => '2019-03-20 11:00:20',
                    'updated_at' => '2019-03-20 11:00:20',
                ),
        ));

        \DB::table('resource_group_accesses')->insert(array (
            0 => 
                array (
                    'id' => 179,
                    'resource_group_id' => 25,
                    'name' => 'Draft Applicants List',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-03-20 11:00:20',
                    'updated_at' => '2019-03-20 11:00:20',
                ), 
            1 => 
                array (
                    'id' => 180,
                    'resource_group_id' => 25,
                    'name' => 'Edit',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-03-20 11:00:20',
                    'updated_at' => '2019-03-20 11:00:20',
                ), 
        ));
    }
}
