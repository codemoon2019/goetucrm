<?php

use Illuminate\Database\Seeder;
use App\Models\Agent;

class UpdatePhoneNoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $agents = User::select('mobile_number')->get();
        foreach ($agents as $agent) {
            $number = $agent->mobile_number;
            $phone_no = substr($number, 1, -1);
            print_r($phone_no . '<br>');
            // Update
        }
        die();
    }
}
