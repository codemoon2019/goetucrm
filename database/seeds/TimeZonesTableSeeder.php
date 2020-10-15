<?php

use Illuminate\Database\Seeder;
use App\Models\TimeZone;

class TimeZonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		TimeZone::create([
            'name' => '(GMT-08:00) Pacific Time (US & Canada)',
            'value' => 'US/Pacific'
        ]);

		TimeZone::create([
            'name' => '(GMT-08:00) Pacific Time (Los Angeles)',
            'value' => 'US/Pacific'
        ]);

		TimeZone::create([
            'name' => '(GMT-07:00) Mountain Standard Time (US & Canada)',
            'value' => 'US/Mountain'
        ]);

		TimeZone::create([
            'name' => '(GMT-06:00) Mountain Daylight Time (US & Canada)',
            'value' => 'US/Central'
        ]);

		TimeZone::create([
            'name' => '(GMT-06:00) Central Time (Chicago)',
            'value' => 'US/Central'
        ]);

		TimeZone::create([
            'name' => '(GMT-06:00) Central Time (US & Canada)',
            'value' => 'US/Central'
        ]);

		TimeZone::create([
            'name' => '(GMT-05:00) Eastern Time (US & Canada)',
            'value' => 'US/Eastern'
        ]);

		TimeZone::create([
            'name' => '(GMT-05:00) Eastern Time (New York)',
            'value' => 'US/Eastern'
        ]);

		TimeZone::create([
            'name' => '(GMT+08:00) Hong Kong',
            'value' => 'Asia/Hong_Kong'
        ]);

		TimeZone::create([
            'name' => '(GMT+08:00) Manila',
            'value' => 'Asia/Manila'
        ]);


    }
}
