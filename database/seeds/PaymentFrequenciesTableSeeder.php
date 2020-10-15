<?php

use Illuminate\Database\Seeder;
use App\Models\PaymentFrequency;

class PaymentFrequenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentFrequency::create([
            'name' => 'Monthly',
            'days' => '30',
            'status' => 'A',
            'sequence' => '3'
        ]);

		PaymentFrequency::create([
            'name' => 'Semi-Montly',
            'days' => '15',
            'status' => 'A',
            'sequence' => '4'
        ]);

		PaymentFrequency::create([
            'name' => 'Quarterly',
            'days' => '90',
            'status' => 'A',
            'sequence' => '5'
        ]);

		PaymentFrequency::create([
            'name' => 'Annually',
            'days' => '365',
            'status' => 'A',
            'sequence' => '6'
        ]);

		PaymentFrequency::create([
            'name' => 'Daily',
            'days' => '1',
            'status' => 'A',
            'sequence' => '1'
        ]);

		PaymentFrequency::create([
            'name' => 'Weekly',
            'days' => '7',
            'status' => 'A',
            'sequence' => '2'
        ]);

		PaymentFrequency::create([
            'name' => 'One-Time',
            'days' => '1',
            'status' => 'A',
            'sequence' => '0'
        ]);

    }
}
