<?php

use App\Models\MerchantStatus;
use Illuminate\Database\Seeder;

class MerchantStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'description' => 'Boarding',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'created_at' => '2019-03-17 00:00:00',
                'updated_at' => '2019-03-17 00:00:00',
            ],
            [
                'id' => 2,
                'description' => 'For Approval',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'created_at' => '2019-03-17 00:00:00',
                'updated_at' => '2019-03-17 00:00:00',
            ],
            [
                'id' => 3,
                'description' => 'Boarded',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'created_at' => '2019-03-17 00:00:00',
                'updated_at' => '2019-03-17 00:00:00',
            ],
            [
                'id' => 4,
                'description' => 'Live',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'created_at' => '2019-03-17 00:00:00',
                'updated_at' => '2019-03-17 00:00:00',
            ],
            [
                'id' => 5,
                'description' => 'Declined',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'created_at' => '2019-03-17 00:00:00',
                'updated_at' => '2019-03-17 00:00:00',
            ],
            [
                'id' => 6,
                'description' => 'Cancelled',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'created_at' => '2019-03-17 00:00:00',
                'updated_at' => '2019-03-17 00:00:00',
            ],
        ];

        MerchantStatus::insert($data);
    }
}
