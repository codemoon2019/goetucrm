<?php

use Illuminate\Database\Seeder;
use App\Models\PartnerStatus;

class PartnerStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PartnerStatus::create([
            'name' => 'New',
            'description' => 'New',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        PartnerStatus::create([
            'name' => 'Processed',
            'description' => 'Processed',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        PartnerStatus::create([
            'name' => 'Approved',
            'description' => 'Approved',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        PartnerStatus::create([
            'name' => 'Pending',
            'description' => 'Pending',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        PartnerStatus::create([
            'name' => 'Terminated',
            'description' => 'Terminated',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        PartnerStatus::create([
            'name' => 'Dead',
            'description' => 'Dead',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

    }
}
