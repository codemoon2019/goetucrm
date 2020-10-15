<?php

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();

        foreach ($products as $product) { 
            if ($product->parent_id === -1) {
                $product->update(['code' => 'P' . sprintf('%08d', $product->id)]);
            } else {
                $product->update(['code' => 'SP' . sprintf('%08d', $product->id)]);
            }
        }
    }
}
