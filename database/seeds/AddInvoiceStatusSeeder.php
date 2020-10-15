<?php

use Illuminate\Database\Seeder;
use App\Models\InvoiceStatus;

class AddInvoiceStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InvoiceStatus::create([
            'code' => 'L',
            'description' => 'Partial Paid',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);
    }
}
