<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;
use App\Models\User;
use App\Models\Partner;

class UpdateUsernameTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tables = array_map('reset', DB::select('SHOW TABLES'));

        foreach ($tables as $table) {
            try {
                $resources = DB::table($table)
                    ->where('create_by', 'like', 'U0%')
                    ->get();

                foreach ($resources as $resource) {
                    $username = explode("U", $resource->create_by);
                    $username = 'U1'. $username[1];

                    DB::table($table)
                        ->where('id', $resource->id)
                        ->update(['create_by' => $username]);
                }
            } catch (\Exception $ex) {
                Log::warning($ex->getMessage());
                continue;
            }
            

            try {
                $resources = DB::table($table)
                    ->where('update_by', 'like', 'U0%')
                    ->get();

                foreach ($resources as $resource) {
                    $username = explode("U", $resource->update_by);
                    $username = 'U1'. $username[1];

                DB::table($table)
                        ->where('id', $resource->id)
                        ->update(['update_by' => $username]);
                }
            } catch (\Exception $ex) {
                Log::warning($ex->getMessage());
                continue;
            }
        }

        $users = User::where('username', 'like', 'U%')->get();
        foreach ($users as $user) {
            $username = explode("U1", $user->username);
            $username = 'U1'. sprintf('%07d', $user->id);
            
            User::where('id', $user->id)
                ->update(['username' => $username]);
        }

        $partners = Partner::where('partner_id_reference', 'like', 'U0%')->get();
        foreach ($partners as $partner) {
            $username = explode("U", $user->partner_id_reference);
            $username = 'U1'. $username[1];
            
            Partner::where('id', $partner->id)
                ->update(['username' => $username]);
        }

        $notifications = Notification::where('recipient', 'like', 'U0%')->get();
        foreach ($partners as $partner) {
            $username = explode("U", $user->partner_id_reference);
            $username = 'U1'. $username[1];
            
            Notification::where('id', $partner->id)
                ->update(['username' => $username]);
        }
    }
}
