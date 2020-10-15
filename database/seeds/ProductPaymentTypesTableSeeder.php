<?php

use Illuminate\Database\Seeder;
use App\Models\ProductPaymentType;

class ProductPaymentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductPaymentType::create([
            'name' => 'Setup Fee',
            'description' => 'Setup Fee',
            'status' => 'A',
            'sequence' => 1
        ]);

        ProductPaymentType::create([
            'name' => 'Monthly Fee',
            'description' => 'Monthly Fee',
            'status' => 'A',
            'sequence' => 2
        ]);

        ProductPaymentType::create([
            'name' => 'Yearly Fee',
            'description' => 'Yearly Fee',
            'status' => 'A',
            'sequence' => 3
        ]);

        ProductPaymentType::create([
            'name' => 'Prepaid',
            'description' => 'Prepaid',
            'status' => 'A',
            'sequence' => 4
        ]);

    }
}
