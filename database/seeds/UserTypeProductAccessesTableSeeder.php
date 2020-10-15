<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\UserTypeProductAccess;

class UserTypeProductAccessesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Get user type product accesses from old database
         */
        $userTypeProductAccesses = DB::connection('mysql_old')->table('user_type_product_access')->get();

        if(isset($userTypeProductAccesses)) {
            foreach($userTypeProductAccesses as $userTypeProductAccess){

                /**
                 * Insert user type product access from new database
                 */
                UserTypeProductAccess::create([
                    'id' => $userTypeProductAccess->id,
                    'user_type_id' => $userTypeProductAccess->user_type_id,
                    'product_id' => $userTypeProductAccess->product_id,
                    'create_by' => $userTypeProductAccess->create_by
                ]);

            }
        }


    }
}
