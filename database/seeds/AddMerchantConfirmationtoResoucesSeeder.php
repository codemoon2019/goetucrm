<?php

use Illuminate\Database\Seeder;

class AddMerchantConfirmationtoResoucesSeeder extends Seeder
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
                    'id' => 306,
                    'resource' => 'merchants/board_merchant',
                    'description' => 'Board Merchant',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 6,
                    'resource_group_access_id' => 169,
                    'update_by' => 'admin',
                    'created_at' => '2019-02-12 11:00:00',
                    'updated_at' => '2019-02-12 11:00:00',
                ),
            1 => 
                array (
                    'id' => 307,
                    'resource' => 'merchants/approve_merchant',
                    'description' => 'Approve Merchant',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 6,
                    'resource_group_access_id' => 170,
                    'update_by' => 'admin',
                    'created_at' => '2019-02-12 11:00:00',
                    'updated_at' => '2019-02-12 11:00:00',
                ),

        ));

        \DB::table('resource_group_accesses')->insert(array (
            0 => 
                array (
                    'id' => 169,
                    'resource_group_id' => 6, 
                    'name' => 'Board Merchant',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-02-12 10:05:00',
                    'updated_at' => '2019-02-12 10:05:00',
                ), 
            1 => 
                array (
                    'id' => 170,
                    'resource_group_id' => 6, 
                    'name' => 'Approve Merchant',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-02-12 10:05:00',
                    'updated_at' => '2019-02-12 10:05:00',
                ), 


        ));
    }
}
