<?php

use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class DisableCashPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentType::where('name', 'Cash')
            ->update([
                'status' => 'I',
                'update_by' => 'admin'
            ]);

    }
}
