<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'username' => 'admin',
                'password' => '$2y$10$D5nesThxhqpsQYKmOaLSbeXIT9G4HqXAVT7PYKXGQCE0TBSGaNfyy',
                'last_name' => 'Administrator',
                'first_name' => 'GoETU',
                'email_address' => 'go3dev@go3solutions.com',
                'mobile_number' => '-123-232-3232',
                'user_type_id' => '1',
                'reference_id' => -1,
                'create_by' => 'Seeder',
                'update_by' => 'admin',
                'status' => 'A',
                'is_iso' => 0,
                'is_merchant' => 0,
                'is_customer' => 0,
                'is_admin' => 0,
                'is_agent' => NULL,
                'is_not_default' => 0,
                'require_change_password' => 0,
                'allow_status_override' => 1,
                'is_verified_email' => 1,
                'is_verified_mobile' => 1,
                'ein' => NULL,
                'ssn' => NULL,
                'business_address1' => NULL,
                'business_address2' => NULL,
                'city' => NULL,
                'state' => NULL,
                'zip' => NULL,
                'country' => 'China',
                'business_phone1' => NULL,
                'extension' => NULL,
                'business_phone2' => NULL,
                'fax' => NULL,
                'mail_address1' => NULL,
                'mail_address2' => NULL,
                'mail_city' => NULL,
                'mail_state' => NULL,
                'mail_zip' => NULL,
                'mail_country' => NULL,
                'home_address1' => 'Address 1',
                'home_address2' => '',
                'home_city' => 'City',
                'home_state' => 'AK',
                'home_zip' => '12345',
                'home_country' => 'United States',
                'home_landline' => '',
                'country_code' => '86',
                'dob' => '2001-01-01',
                'favorites' => NULL,
                'is_partner' => 0,
                'is_online' => 1,
                'temp_socket_id' => 'h9KH3OH9amw6sdH3AAA_',
                'remember_token' => 'bWVTW4xmYuadXlGgYy15xNRlPStknen9UprbfhxqIMGbcQj992JkNDoXqRnC',
                'last_activity' => '2018-08-09 16:29:46',
                'created_at' => '2018-07-30 05:28:28',
                'updated_at' => '2018-08-09 16:29:46',
            )
        ));
        
        
    }
}