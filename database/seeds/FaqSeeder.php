<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('resource_group_accesses')->insert([
            149 => [
                'id' => 158,
                'resource_group_id' => 19,
                'name' => 'Edit Ticket FAQ',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'created_at' => '2017-08-11 00:30:04',
                'updated_at' => '2018-06-07 01:36:20',
            ],

            150 => [
                'id' => 159,
                'resource_group_id' => 19,
                'name' => 'View Ticket FAQ',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'created_at' => '2017-08-11 00:30:04',
                'updated_at' => '2018-06-07 01:36:20',
            ],
        ]);

        DB::table('resources')->insert([
            288 => [
                'id' => 295,
                'resource' => 'tickets/faq/edit',
                'description' => 'Edit Ticket FAQ',
                'create_by' => 'Seeder',
                'deleted' => 0,
                'resource_group_id' => 19,
                'resource_group_access_id' => 158,
                'update_by' => 'admin',
                'created_at' => '2018-10-02 11:00:00',
                'updated_at' => '2018-10-02 11:00:00',
            ],

            289 => [
                'id' => 296,
                'resource' => 'tickets/faq',
                'description' => 'View Ticket FAQ',
                'create_by' => 'Seeder',
                'deleted' => 0,
                'resource_group_id' => 19,
                'resource_group_access_id' => 159,
                'update_by' => 'admin',
                'created_at' => '2018-10-02 11:00:00',
                'updated_at' => '2018-10-02 11:00:00',
            ],
        ]);
    }
}
