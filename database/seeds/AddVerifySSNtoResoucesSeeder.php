<?php

use Illuminate\Database\Seeder;

class AddVerifySSNtoResoucesSeeder extends Seeder
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
                    'id' => 305,
                    'resource' => '',
                    'description' => 'Verify Contact SSN',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 6,
                    'resource_group_access_id' => 168,
                    'update_by' => 'admin',
                    'created_at' => '2019-02-07 11:00:00',
                    'updated_at' => '2019-02-07 11:00:00',
                ),
        ));

        \DB::table('resource_group_accesses')->insert(array (
            0 => 
                array (
                    'id' => 168,
                    'resource_group_id' => 6, // SubISO
                    'name' => 'Verify Contact SSN',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-02-07 10:05:00',
                    'updated_at' => '2019-02-07 10:05:00',
                ), 

        ));
    }
}
