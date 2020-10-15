<?php

use Illuminate\Database\Seeder;
use App\Models\Country;

class UpdateDisplayOnToCountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [            
            ['id' => 1, 'display_on_partner' => 1],
            ['id' => 1, 'display_on_merchant' => 1],
            ['id' => 1, 'display_on_others' => 1],
            ['id' => 1, 'display_on_users' => 1],

            ['id' => 2, 'display_on_users' => 1],

            ['id' => 3, 'display_on_users' => 1],
        ];

        foreach ($items as $item) {
            Country::where('id', $item['id'])->update($item);
        }
    }
}
