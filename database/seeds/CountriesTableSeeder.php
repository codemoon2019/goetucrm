<?php

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('countries')->delete();

        Country::create([
            'name' => 'United States',
            'iso_code_2' => 'US',
            'iso_code_3' => 'USA',
            'country_calling_code' => '1',
            'status' => 'A',
            'validate_number' => 1
        ]);

        Country::create([
            'name' => 'Philippines',
            'iso_code_2' => 'PH',
            'iso_code_3' => 'PHL',
            'country_calling_code' => '63',
            'status' => 'A',
            'validate_number' => 1
        ]);

        Country::create([
            'name' => 'China',
            'iso_code_2' => 'CN',
            'iso_code_3' => 'CHN',
            'country_calling_code' => '86',
            'status' => 'A',
            'validate_number' => 0
        ]);

    }
}
