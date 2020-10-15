<?php

use Illuminate\Database\Seeder;
use App\Models\State;

class AdditionalStatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        State::create([
            'name' => 'Beijing',
            'abbr' => 'BJ',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Chongqing',
            'abbr' => 'CQ',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Guangxi',
            'abbr' => 'GX',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Inner Mongolia',
            'abbr' => 'NM',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Hongkong',
            'abbr' => 'HK',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Ningxia',
            'abbr' => 'NX',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Shanghai',
            'abbr' => 'SHG',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Tianjin',
            'abbr' => 'TJ',
            'country' => 'CN'
        ]);
        State::create([
            'name' => 'Tibet',
            'abbr' => 'XZG',
            'country' => 'CN'
        ]);

        State::create([
            'name' => 'Xinjiang',
            'abbr' => 'XJ',
            'country' => 'CN'
        ]);

        State::create([
            'name' => 'Zhejiang',
            'abbr' => 'ZJ',
            'country' => 'CN'
        ]);

    }
}




