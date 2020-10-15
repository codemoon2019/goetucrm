<?php

use Illuminate\Database\Seeder;
use App\Models\BankAccountType;

class BankAccountTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BankAccountType::create([
            'code' => 'BIZ',
            'name' => 'Business Checking',
            'description' => 'Business Checking',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        BankAccountType::create([
            'code' => 'GL',
            'name' => 'General Ledger',
            'description' => 'General Ledger',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

        BankAccountType::create([
            'code' => 'SAVINGS',
            'name' => 'Savings',
            'description' => 'Savings',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder'
        ]);

    }
}
