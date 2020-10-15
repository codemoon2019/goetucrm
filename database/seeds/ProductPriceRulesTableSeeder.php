<?php

use Illuminate\Database\Seeder;
use App\Models\ProductPriceRule;

class ProductPriceRulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductPriceRule::create([
            'name' => 'Fixed',
            'description' => 'Buy rate should be in specific amount',
            'status' => 'A'
        ]);

        ProductPriceRule::create([
            'name' => 'Range',
            'description' => 'Price should not be greater and lesser than the set amount',
            'status' => 'A'
        ]);

    }
}
