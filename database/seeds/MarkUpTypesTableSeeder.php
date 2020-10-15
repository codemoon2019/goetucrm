<?php

use Illuminate\Database\Seeder;
use App\Models\MarkUpType;

class MarkUpTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MarkUpType::create([
            'name' => 'Mark up to downline',
            'description' => 'All mark up will be given to downline',
            'status' => 'I'
        ]);

        MarkUpType::create([
            'name' => 'Split Mark up',
            'description' => 'Mark up should be splitted according to percentage. The percentage stated will be given to upline.',
            'status' => 'I'
        ]);

        MarkUpType::create([
            'name' => 'First Buy Rate',
            'description' => 'All mark up will be given to upline.',
            'status' => 'A'
        ]);

        MarkUpType::create([
            'name' => 'Second Buy Rate',
            'description' => 'Second buy rate whole',
            'status' => 'A'
        ]);



    }
}
