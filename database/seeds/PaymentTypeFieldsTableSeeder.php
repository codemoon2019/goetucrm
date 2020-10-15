<?php

use Illuminate\Database\Seeder;
use App\Models\PaymentTypeField;

class PaymentTypeFieldsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentTypeField::create([
            'payment_type_id' => '1',
            'field_data' => '<label>Bank Name:</label><input type="text" class="form-control pt-ach" id="txtBankNameACH{id}" name="txtBankNameACH{id}">',
            'sequence' => '1'
        ]);

        PaymentTypeField::create([
            'payment_type_id' => '1',
            'field_data' => '<label>Routing Number:</label><input type="text" class="form-control pt-ach-rn" id="txtRoutingNumberACH{id}" name="txtRoutingNumberACH{id}">',
            'sequence' => '2'
        ]);

        PaymentTypeField::create([
            'payment_type_id' => '1',
            'field_data' => '<label>Bank Account Number:</label><input type="text" class="form-control pt-ach-rn" id="txtBankAccountNumberACH{id}" name="txtBankAccountNumberACH{id}">',
            'sequence' => '3'
        ]);


    }
}
