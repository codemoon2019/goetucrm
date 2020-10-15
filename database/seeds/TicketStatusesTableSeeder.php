<?php

use Illuminate\Database\Seeder;
use App\Models\TicketStatus;
use Illuminate\Support\Facades\DB;
use App\Contracts\Constant;

class TicketStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TicketStatus::truncate();

        $data = [
            [
                'code' => 'N',
                'description' => 'New',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'is_action' => false
            ],
            [
                'code' => 'I',
                'description' => 'In Progress',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'is_action' => true
            ],
            [
                'code' => 'P',
                'description' => 'Pending',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'is_action' => true
            ],
            [
                'code' => 'S',
                'description' => 'Solved',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'is_action' => true
            ],
            [
                'code' => 'M',
                'description' => 'Merged',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'is_action' => false
            ],
            [
                'code' => 'D',
                'description' => 'Deleted',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A',
                'is_action' => false
            ]
        ];

        TicketStatus::insert($data);
    }
}
