<?php

use Illuminate\Database\Seeder;

class ResourceGroupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('resource_groups')->delete();
        
        \DB::table('resource_groups')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Admin',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Agent',
                'partner_type_access' => '2',
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Department',
                'partner_type_access' => NULL,
                'status' => 'I',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Lead',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Logout',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Merchant',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Notification',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Company',
                'partner_type_access' => '1,2,3,4,5,6,8',
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Product',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Profile',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Settings',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Prospect',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Sub Agent',
                'partner_type_access' => '3',
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'ISO',
                'partner_type_access' => '1,2,3,5,6,8',
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Sub ISO',
                'partner_type_access' => '1,2,3,6,8',
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Users',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Training',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Billing',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'Ticketing',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Purchase Order',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'Receiving Purchase Order',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'My Calendar',
                'partner_type_access' => NULL,
                'status' => 'A',
                'create_by' => 'Seeder',
                'update_by' => NULL,
                'created_at' => '2018-06-07 01:36:20',
                'updated_at' => '2018-06-07 01:36:20',
            ),
        ));
        
        
    }
}