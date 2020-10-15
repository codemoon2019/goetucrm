<?php

use Illuminate\Database\Seeder;

class AddOrderStartEndDateAccessSeeder extends Seeder
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
                    'id' => 293,
                    'resource' => '',
                    'description' => 'Order Payment Frequency Edit',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 6,
                    'resource_group_access_id' => 156,
                    'update_by' => 'admin',
                    'created_at' => '2019-01-14 11:00:00',
                    'updated_at' => '2019-01-14 11:00:00',
                ),
        ));
        
        \DB::table('resource_group_accesses')->insert(array (
            0 => 
                array (
                    'id' => 156,
                    'resource_group_id' => 6,
                    'name' => 'Order Payment Frequency Edit',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-01-14 11:00:00',
                    'updated_at' => '2019-01-14 11:00:00',
                ),

        ));
        
    }
}