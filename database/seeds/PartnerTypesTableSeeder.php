<?php

use Illuminate\Database\Seeder;

class PartnerTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('partner_types')->delete();
        
        \DB::table('partner_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'AGENT',
                'description' => 'AGENT',
                'status' => 'A',
                'create_by' => 'Seeder',
                'user_type_id' => 6,
                'sequence' => 3,
                'included_in_partners' => 1,
                'upline' => '4,5,7',
                'included_in_agents' => 1,
                'included_in_leads' => 0,
                'included_in_training' => 1,
                'initial' => 'A',
                'created_at' => '2018-06-13 02:43:11',
                'updated_at' => '2018-06-13 02:43:11',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'SUB AGENT',
                'description' => 'SUB AGENT',
                'status' => 'A',
                'create_by' => 'Seeder',
                'user_type_id' => 13,
                'sequence' => 4,
                'included_in_partners' => 1,
                'upline' => '1,4,5,7',
                'included_in_agents' => 1,
                'included_in_leads' => 0,
                'included_in_training' => 1,
                'initial' => 'SA',
                'created_at' => '2018-06-13 02:43:11',
                'updated_at' => '2018-06-13 02:43:11',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'MERCHANT',
                'description' => 'MERCHANT',
                'status' => 'A',
                'create_by' => 'Seeder',
                'user_type_id' => 8,
                'sequence' => 5,
                'included_in_partners' => 0,
                'upline' => '1,2,4,5,7',
                'included_in_agents' => 0,
                'included_in_leads' => 0,
                'included_in_training' => 1,
                'initial' => 'M',
                'created_at' => '2018-06-13 02:43:11',
                'updated_at' => '2018-06-13 02:43:11',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'ISO',
                'description' => 'ISO',
                'status' => 'A',
                'create_by' => 'Seeder',
                'user_type_id' => 4,
                'sequence' => 1,
                'included_in_partners' => 1,
                'upline' => '7',
                'included_in_agents' => 0,
                'included_in_leads' => 0,
                'included_in_training' => 1,
                'initial' => 'I',
                'created_at' => '2018-06-13 02:43:11',
                'updated_at' => '2018-06-13 02:43:11',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'SUB ISO',
                'description' => 'SUB ISO',
                'status' => 'A',
                'create_by' => 'Seeder',
                'user_type_id' => 5,
                'sequence' => 2,
                'included_in_partners' => 1,
                'upline' => '4,7',
                'included_in_agents' => 0,
                'included_in_leads' => 0,
                'included_in_training' => 1,
                'initial' => 'SI',
                'created_at' => '2018-06-13 02:43:11',
                'updated_at' => '2018-06-13 02:43:11',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'LEAD',
                'description' => 'LEAD',
                'status' => 'A',
                'create_by' => 'Seeder',
                'user_type_id' => 10,
                'sequence' => 7,
                'included_in_partners' => 0,
                'upline' => '1,2,4,5,7',
                'included_in_agents' => 0,
                'included_in_leads' => 1,
                'included_in_training' => NULL,
                'initial' => 'L',
                'created_at' => '2018-06-13 02:43:11',
                'updated_at' => '2018-06-13 02:43:11',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'COMPANY',
                'description' => 'COMPANY',
                'status' => 'A',
                'create_by' => 'Seeder',
                'user_type_id' => 11,
                'sequence' => 0,
                'included_in_partners' => 1,
                'upline' => '-1',
                'included_in_agents' => 0,
                'included_in_leads' => 0,
                'included_in_training' => NULL,
                'initial' => 'C',
                'created_at' => '2018-06-13 02:43:11',
                'updated_at' => '2018-06-13 02:43:11',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'PROSPECT',
                'description' => 'PROSPECT',
                'status' => 'A',
                'create_by' => 'Seeder',
                'user_type_id' => 12,
                'sequence' => 6,
                'included_in_partners' => 0,
                'upline' => '1,2,4,5,7',
                'included_in_agents' => 0,
                'included_in_leads' => 1,
                'included_in_training' => NULL,
                'initial' => 'P',
                'created_at' => '2018-06-13 02:43:11',
                'updated_at' => '2018-06-13 02:43:11',
            ),
        ));
        
        
    }
}