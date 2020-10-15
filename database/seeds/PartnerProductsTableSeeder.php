<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PartnerProduct;

class PartnerProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $partnerProducts = DB::connection('mysql_old')->table('partner_product')->get();

        if (isset($partnerProducts)) {
            foreach ($partnerProducts as $partnerProduct) {
                PartnerProduct::create([
                    'id' => $partnerProduct->id,
                    'partner_id' => $partnerProduct->partner_id,
                    'product_id' => $partnerProduct->product_id,
                    'buy_rate' => $partnerProduct->buy_rate,
                    'payment_frequency' => $partnerProduct->payment_frequency,
                    'days_before_due_date' => 0,
                    'due_date' => $partnerProduct->due_date,
                    'created_at' => $partnerProduct->create_date,
                    'create_by' => $partnerProduct->create_by,
                    'status' => $partnerProduct->status,
                    'mark_up_type_id' => $partnerProduct->mark_up_type_id,
                    'price_rule_type_id' => 0,
                    'mark_up_value' => 0,
                    'price_value_min' => 0,
                    'price_value_max' => 0,
                    'split_type' => $partnerProduct->split_type,
                    'is_split_percentage' => $partnerProduct->is_split_percentage,
                    'other_buy_rate' => $partnerProduct->other_buy_rate,
                    'downline_buy_rate' => $partnerProduct->downline_buy_rate,
                    'upline_percentage' => $partnerProduct->upline_percentage,
                    'downline_percentage' => $partnerProduct->downline_percentage,
                    'pricing_option' => $partnerProduct->pricing_option,
                    'price' => $partnerProduct->price,
                    'update_by' => 'seeder'
                ]);
            }
        }

    }
}
