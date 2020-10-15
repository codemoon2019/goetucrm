<?php

use Illuminate\Database\Seeder;
use App\Models\InvoiceStatus;
use Illuminate\Support\Facades\DB;

class InvoiceStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('invoice_statuses')->truncate();

        InvoiceStatus::create([
            'code' => 'P',
            'description' => 'Paid',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceStatus::create([
            'code' => 'U',
            'description' => 'Unpaid',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);
        
        InvoiceStatus::create([
            'code' => 'O',
            'description' => 'Overdue',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);
        
        InvoiceStatus::create([
            'code' => 'C',
            'description' => 'Voided',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);
        
        InvoiceStatus::create([
            'code' => 'R',
            'description' => 'Rejected',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);
        
        InvoiceStatus::create([
            'code' => 'S',
            'description' => 'Processing',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);
        
        InvoiceStatus::create([
            'code' => 'X',
            'description' => 'Refund',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceStatus::create([
            'code' => 'L',
            'description' => 'Partial Paid',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        InvoiceStatus::create([
            'code' => 'H',
            'description' => 'For Confirmation',
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

    }
}
