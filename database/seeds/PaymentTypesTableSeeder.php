<?php

use Illuminate\Database\Seeder;
use App\Models\PaymentType;

class PaymentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentType::create([
            'name' => 'ACH',
            'description' => 'ACH',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder',
            'header_fields' => 'Bank Name~Routing Number~Bank Account#~Default Payment',
            'header_values' => 'bank_name~routing_number~bank_account_number~is_default_payment'
        ]);

        PaymentType::create([
            'name' => 'Cash',
            'description' => 'Cash',
            'status' => 'A',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder',
            'header_fields' => 'Default Payment',
            'header_values' => 'is_default_payment'
        ]);

        PaymentType::create([
            'name' => 'Credit Card',
            'description' => 'Credit Card',
            'status' => 'I',
            'create_by' => 'Seeder',
            'update_by' => 'Seeder',
            'header_fields' => "Cardholder's Name~Credit Card No.~CVV~Expiration Date~Address 1~Default Payment",
            'header_values' => 'cardholder_name~credit_card_no~cvv~expiration_date~address_1~is_default_payment'
        ]);


    }
}
