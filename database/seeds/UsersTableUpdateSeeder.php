<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = DB::table('users')->where('username', 'like', 'M%')->get(['username', 'reference_id']);
        foreach ($rows as $row) {
            DB::table('partners')
                ->where('id', $row->reference_id)
                ->update(['partner_id_reference' => $row->username]);
        }
    }
}
