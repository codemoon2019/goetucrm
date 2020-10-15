<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Contracts\Constant;
use App\Models\ProductCategory;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // /**
        //  * Get all products from old database
        //  */
        // $products = DB::connection('mysql_old')->table('products')->get();

        // if (isset($products)) {
        //     foreach ($products as $product) {

        //         /**
        //          * Insert products to new database
        //          */
        //         Product::create([
        //             'id' => $product->id,
        //             'name' => $product->name,
        //             'description' => $product->description,
        //             'parent_id' => $product->parent_id,
        //             'create_by' => Constant::DEFAULT_CREATE_BY,
        //             'update_by' => Constant::DEFAULT_UPDATE_BY,
        //             'status' => ($product->status == Constant::DEFAULT_STATUS_ACTIVE) ? Constant::DEFAULT_STATUS_ACTIVE : Constant::DEFAULT_STATUS_DELETED,
        //             'buy_rate' => $product->buy_rate,
        //             'product_category_id' => $product->product_category_id,
        //             'product_type' => $product->product_type,
        //             'sequence' => $product->sequence,
        //             'hide_field' => $product->hide_field,
        //             'company_id' => $product->company_id,
        //             'product_type_id' => $product->product_type_id,
        //             'field_identifier' => (isset($product->field_identifier) ? $product->field_identifier : null)
        //         ]);
        //     }
        // }
    }

    /**
     * Update product parent_id from old to new id
     */
    protected function updateProduct()
    {
        /**
         * List all products
         */
        $products = Product::all();

        foreach ($products as $product) {
            /**
             * if parent_id equal to id changed parent_id to id
             */
            $changeProducts = Product::where('old_id', '=', $product->parent_id)->first();
            if (isset($changeProducts)) {
                /**
                 * Update product parent_id to new product id
                 */
                Product::where('parent_id', '=', $changeProducts->old_id)->update(['parent_id' => $changeProducts->id]);
            }

        }
    }
}
