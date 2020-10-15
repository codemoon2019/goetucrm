<?php

use Illuminate\Database\Seeder;

class UserTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user_types')->delete();
        
        \DB::table('user_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'description' => 'SUPER ADMIN',
                'create_by' => 'SYSTEM',
                'update_by' => 'admin',
                'status' => 'A',
                'partner_type_access' => '2,1,3,4,5,6,8',
                'created_at' => '2018-07-30 05:28:28',
                'updated_at' => '2018-09-03 04:38:30',
                'company_id' => -1,
                'parent_id' => -1,
                'head_id' => -1,
            ),
            1 => 
            array (
                'id' => 3,
                'description' => 'CUSTOMER',
                'create_by' => 'SYSTEM',
                'update_by' => 'Seeder',
                'status' => 'I',
                'partner_type_access' => NULL,
                'created_at' => '2018-07-30 05:28:28',
                'updated_at' => '2018-07-30 05:28:28',
                'company_id' => -1,
                'parent_id' => -1,
                'head_id' => -1,
            ),
            2 => 
            array (
                'id' => 4,
                'description' => 'ISO',
                'create_by' => 'SYSTEM',
                'update_by' => 'U0000004',
                'status' => 'A',
                'partner_type_access' => '2,1,3,5,6,8',
                'created_at' => '2018-07-30 05:28:28',
                'updated_at' => '2018-08-09 03:41:50',
                'company_id' => -1,
                'parent_id' => -1,
                'head_id' => -1,
            ),
            3 => 
            array (
                'id' => 5,
                'description' => 'SUB ISO',
                'create_by' => 'SYSTEM',
                'update_by' => 'U0000004',
                'status' => 'A',
                'partner_type_access' => '2,3,1,6,8',
                'created_at' => '2018-07-30 05:28:28',
                'updated_at' => '2018-08-09 04:02:32',
                'company_id' => -1,
                'parent_id' => -1,
                'head_id' => -1,
            ),
            4 => 
            array (
                'id' => 6,
                'description' => 'AGENT',
                'create_by' => 'SYSTEM',
                'update_by' => 'U0000004',
                'status' => 'A',
                'partner_type_access' => '2,3',
                'created_at' => '2018-07-30 05:28:28',
                'updated_at' => '2018-08-09 05:06:38',
                'company_id' => -1,
                'parent_id' => -1,
                'head_id' => -1,
            ),
            5 => 
            array (
                'id' => 7,
                'description' => 'MERCHANT SUPER ADMIN',
                'create_by' => 'SYSTEM',
                'update_by' => 'Seeder',
                'status' => 'I',
                'partner_type_access' => '',
                'created_at' => '2018-07-30 05:28:28',
                'updated_at' => '2018-07-30 05:28:28',
                'company_id' => -1,
                'parent_id' => -1,
                'head_id' => -1,
            ),
            6 => 
            array (
                'id' => 8,
                'description' => 'MERCHANT',
                'create_by' => 'SYSTEM',
                'update_by' => 'U0000041',
                'status' => 'A',
                'partner_type_access' => '',
                'created_at' => '2018-07-30 05:28:28',
                'updated_at' => '2018-09-05 01:57:54',
                'company_id' => -1,
                'parent_id' => -1,
                'head_id' => -1,
            ),
            7 => 
            array (
                'id' => 9,
                'description' => 'ADMIN',
                'create_by' => 'SYSTEM',
                'update_by' => 'Seeder',
                'status' => 'I',
                'partner_type_access' => '1,2,3,4,5',
                'created_at' => '2018-07-30 05:28:28',
                'updated_at' => '2018-07-30 05:28:28',
                'company_id' => -1,
                'parent_id' => -1,
                'head_id' => -1,
            ),
            8 => 
            array (
                'id' => 10,
                'description' => 'LEAD',
                'create_by' => 'SYSTEM',
                'update_by' => 'Seeder',
                'status' => 'A',
                'partner_type_access' => NULL,
                'created_at' => '2018-07-30 05:28:28',
                'updated_at' => '2018-07-30 05:28:28',
                'company_id' => -1,
                'parent_id' => -1,
                'head_id' => -1,
            ),
            9 => 
            array (
                'id' => 11,
                'description' => 'COMPANY',
                'create_by' => 'SYSTEM',
                'update_by' => 'U0000026',
                'status' => 'A',
                'partner_type_access' => '2,1,3,4,5,6,8',
                'created_at' => '2018-07-30 05:28:28',
                'updated_at' => '2018-08-28 01:10:46',
                'company_id' => -1,
                'parent_id' => -1,
                'head_id' => -1,
            ),
            10 => 
            array (
                'id' => 12,
                'description' => 'PROSPECT',
                'create_by' => 'SYSTEM',
                'update_by' => 'Seeder',
                'status' => 'A',
                'partner_type_access' => NULL,
                'created_at' => '2018-07-30 05:28:28',
                'updated_at' => '2018-07-30 05:28:28',
                'company_id' => -1,
                'parent_id' => -1,
                'head_id' => -1,
            ),
            11 => 
            array (
                'id' => 13,
                'description' => 'SUB AGENT',
                'create_by' => 'SYSTEM',
                'update_by' => 'U0000004',
                'status' => 'A',
                'partner_type_access' => '3',
                'created_at' => '2018-07-30 05:28:28',
                'updated_at' => '2018-08-09 06:13:54',
                'company_id' => -1,
                'parent_id' => -1,
                'head_id' => -1,
            )
        ));
        
        
    }
}