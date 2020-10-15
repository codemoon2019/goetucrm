<?php

use Illuminate\Database\Seeder;

class AddViewUplinetoResourcesTableSeeder extends Seeder
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
                    'id' => 308,
                    'resource' => '',
                    'description' => 'View Upline',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 6,
                    'resource_group_access_id' => 171,
                    'update_by' => 'admin',
                    'created_at' => '2019-03-13 15:00:00',
                    'updated_at' => '2019-03-13 15:00:00',
                ),
        ));

        \DB::table('resource_group_accesses')->insert(array (
            0 => 
                array (
                    'id' => 171,
                    'resource_group_id' => 6,
                    'name' => 'View Upline',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-03-13 15:00:00',
                    'updated_at' => '2019-03-13 15:00:00',
                ), 

        ));
    }
}
