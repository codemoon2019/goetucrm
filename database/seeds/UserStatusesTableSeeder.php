<?php

use Illuminate\Database\Seeder;
use App\Models\UserStatus;
use Illuminate\Support\Facades\DB;

class UserStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Get all user statuses from old database
         */
        $userStatuses = DB::connection('mysql_old')->table('user_status')->get();

        if(isset($userStatuses)) {

            foreach ($userStatuses as $userStatus) {

                /**
                 * Insert all user statuses to new database
                 */
                UserStatus::create([
                    'id' => $userStatus->id,
                    'code' => $userStatus->code,
                    'description' => $userStatus->description,
                    'is_deleted' => $userStatus->is_deleted
                ]);

            }

        }

    }
}
