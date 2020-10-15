<?php

use Illuminate\Database\Seeder;
use App\Models\State;

class AdditionalUsStateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        State::create([
            'name' => 'American Samoa',
            'abbr' => 'AS',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'District of Columbia',
            'abbr' => 'DC',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Federated States of Micronesia',
            'abbr' => 'FM',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Guam',
            'abbr' => 'GU',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Marshall Islands',
            'abbr' => 'MH',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Northern Mariana Islands',
            'abbr' => 'MP',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Palau',
            'abbr' => 'PW',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Puerto Rico',
            'abbr' => 'PR',
            'country' => 'US'
        ]);
        State::create([
            'name' => 'Virgin Islands',
            'abbr' => 'VI',
            'country' => 'US'
        ]);
    }
}
