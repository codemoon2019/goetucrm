<?php

use Illuminate\Database\Seeder;

class AddDrafttoMerchantResourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('resource_group_accesses')->insert(array (
            0 => 
                array (
                    'id' => 181,
                    'resource_group_id' => 6,
                    'name' => 'View Draft Merchants',
                    'create_by' => 'Seeder',
                    'update_by' => NULL,
                    'status' => 'A',
                    'created_at' => '2019-04-09 17:45:20',
                    'updated_at' => '2019-04-09 17:45:20',
                ), 
        ));

        DB::table('resources')->insert(array (
            0 => 
                array (
                    'id' => 317,
                    'resource' => '/merchants/draft_merchant',
                    'description' => 'View Draft Merchants',
                    'create_by' => 'Seeder',
                    'deleted' => 0,
                    'resource_group_id' => 6,
                    'resource_group_access_id' => 181,
                    'update_by' => 'admin',
                    'created_at' => '2019-04-09 17:45:20',
                    'updated_at' => '2019-04-09 17:45:20',
                ),
        ));

    }
}
