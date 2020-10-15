<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\UsZipCode;
use App\Models\State;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class UsZipCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('us_zip_codes')->truncate();
        Excel::filter('chunk')->load('storage/zip/us/zip_code_us.xlsx')->chunk(250, function($results)
        {
            $ctr=1;
            foreach($results as $row)
            {
                echo "#{$ctr} ZIP {$row->zip}";
                $now = Carbon::now();
                $state = State::select('id')->where('abbr', $row->state)
                    ->where('country', $row->country)
                    ->first();
                
                UsZipCode::create([
                    'zip_code' => $row->zip,
                    'type' => $row->type,
                    'city' => $row->primary_city,
                    'state_id' => $state->id,
                    'country_id' => 1,
                    'is_primary_city' => 1,
                    'is_acceptable_city' => 0,
                    'county' => $row->county,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // if ( $row->acceptable_cities != null ) {
                //     $cities = explode(', ', $row->acceptable_cities);
                //     foreach ($cities as $city) {
                //         UsZipCode::create([
                //             'zip_code' => $row->zip,
                //             'type' => $row->type,
                //             'city' => $city,
                //             'state_id' => $state->id,
                //             'country_id' => 1,
                //             'is_primary_city' => 0,
                //             'is_acceptable_city' => 1 ,
                //             'county' => $row->county,
                //             'created_at' => $now,
                //             'updated_at' => $now,
                //         ]);
                //     }
                // }
                $ctr++;
            }
        });
    }
}
