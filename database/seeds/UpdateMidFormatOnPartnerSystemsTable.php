<?php

use Illuminate\Database\Seeder;
use App\Models\PartnerSystem;

class UpdateMidFormatOnPartnerSystemsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [            
            ['id' => 1, 'mid_format' => '9999999999999999'],
            ['id' => 2, 'mid_format' => '999999999999'],
            ['id' => 3, 'mid_format' => '9999999999'],
            ['id' => 4, 'mid_format' => '9999'],
        ];

        foreach ($items as $item) {
            PartnerSystem::where('id', $item['id'])->update($item);
        }
    }
}
