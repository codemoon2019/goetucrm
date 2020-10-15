<?php

use Illuminate\Database\Seeder;
use App\Models\InventoryStatus;


class InventoryStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InventoryStatus::create([
            'code' => 'S',
            'description' => 'Saved',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InventoryStatus::create([
            'code' => 'P',
            'description' => 'Posted',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InventoryStatus::create([
            'code' => 'V',
            'description' => 'Voided',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InventoryStatus::create([
            'code' => 'C',
            'description' => 'Closed',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);



    }
}
