<?php

use Illuminate\Database\Seeder;

class DevelopersModuleAccessSeeder extends Seeder
{
    public function run()
    {
        $resourceGroupData = [
            [
                'id' => 28,
                'name' => 'Developers',
                'partner_type_access' => null,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => null,
                'created_at' => '2019-04-24 00:00:00',
                'updated_at' => '2019-04-24 00:00:00',
            ],
            [
                'id' => 29,
                'name' => 'API Documentation',
                'partner_type_access' => null,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => null,
                'created_at' => '2019-04-24 00:00:00',
                'updated_at' => '2019-04-24 00:00:00',
            ]
        ];

        $resourceGroupAccessData = [
            [
                'id' => 205,
                'resource_group_id' => 28,
                'name' => 'View API Keys',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'created_at' => '2019-04-24 00:00:00',
                'updated_at' => '2019-04-24 00:00:00',
                'description' => 'View list of created API Keys'
            ],
            [
                'id' => 206,
                'resource_group_id' => 28,
                'name' => 'Create API Keys',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'created_at' => '2019-04-24 00:00:00',
                'updated_at' => '2019-04-24 00:00:00',
                'description' => 'Create API key to be able to integrate with GoETU CRM'
            ],
            [
                'id' => 207,
                'resource_group_id' => 28,
                'name' => 'View API Documentation',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'created_at' => '2019-04-24 00:00:00',
                'updated_at' => '2019-04-24 00:00:00',
                'description' => 'View list of API Documentation'
            ],
            [
                'id' => 208,
                'resource_group_id' => 29,
                'name' => 'Tickets',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'created_at' => '2019-04-24 00:00:00',
                'updated_at' => '2019-04-24 00:00:00',
                'description' => 'Get All Tickets'
            ],
        ];

        $resourceData = [
            [
                'id' => 340,
                'resource' => 'developers/api-keys',
                'description' => 'View API Keys',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'deleted' => 0,
                'resource_group_id' => 28,
                'resource_group_access_id' => 205,
                'created_at' => '2018-10-02 11:00:00',
                'updated_at' => '2018-10-02 11:00:00',
            ],
            [
                'id' => 341,
                'resource' => 'developers/api-keys',
                'description' => 'Create API Keys',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'deleted' => 0,
                'resource_group_id' => 28,
                'resource_group_access_id' => 206,
                'created_at' => '2018-10-02 11:00:00',
                'updated_at' => '2018-10-02 11:00:00',
            ],
            [
                'id' => 342,
                'resource' => 'developers/api-documentations',
                'description' => 'View API Documentation',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'deleted' => 0,
                'resource_group_id' => 28,
                'resource_group_access_id' => 207,
                'created_at' => '2018-10-02 11:00:00',
                'updated_at' => '2018-10-02 11:00:00',
            ],
            [
                'id' => 343,
                'resource' => 'api-documentation/tickets',
                'description' => 'Get all Tickets',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'deleted' => 0,
                'resource_group_id' => 29,
                'resource_group_access_id' => 208,
                'created_at' => '2018-10-02 11:00:00',
                'updated_at' => '2018-10-02 11:00:00',
            ],
        ];

        DB::table('resource_groups')->insert($resourceGroupData);
        DB::table('resource_group_accesses')->insert($resourceGroupAccessData);
        DB::table('resources')->insert($resourceData);
    }
}
