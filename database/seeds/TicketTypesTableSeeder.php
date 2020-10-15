<?php

use Illuminate\Database\Seeder;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;
use App\Contracts\Constant;

class TicketTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TicketType::truncate();

        $data = [
            [
                'code' => 'I',
                'description' => 'Incident',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A'
            ],
            [
                'code' => 'P',
                'description' => 'Problem',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A'
            ],
            [
                'code' => 'R',
                'description' => 'Request',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A'
            ],
            [
                'code' => 'Q',
                'description' => 'Inquiry',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A'
            ],
            [
                'code' => 'O',
                'description' => 'Others',
                'create_by' => 'Seeder',
                'update_by' => 'Seeder',
                'status' => 'A'
            ],
        ];

        TicketType::insert($data);
    }
}
