<?php

use Illuminate\Database\Seeder;

class AddDivisionAccessSeeder extends Seeder
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
                    'id' => 294,
                    'resource' => '',
                    'description' => 'Divisions',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 1,
                    'resource_group_access_id' => 157,
                    'update_by' => 'admin',
                    'created_at' => '2019-01-14 11:00:00',
                    'updated_at' => '2019-01-14 11:00:00',
                ),
        ));
        
        \DB::table('resource_group_accesses')->insert(array (
            0 => 
                array (
                    'id' => 157,
                    'resource_group_id' => 1,
                    'name' => 'Divisions',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-01-14 11:00:00',
                    'updated_at' => '2019-01-14 11:00:00',
                ),

        ));
    }
}
