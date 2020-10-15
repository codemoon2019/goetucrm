<?php

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Company::create([
            'company_name' => 'EZ2Eat',
            'powered_by_link' => 'https://www.ez2eat.com',
            'logo_path' => 'images/ez2eat_logo.png'
        ]);

        Company::create([
            'company_name' => 'GO3Solutions',
            'powered_by_link' => 'https://www.go3solutions.com',
            'logo_path' => 'images/go3solutions_logo.png'
        ]);

    }
}
