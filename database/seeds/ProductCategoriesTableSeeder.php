<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ProductCategory;
use App\Contracts\Constant;

class ProductCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Product Categories from old database
         */
        $productCategories = DB::connection('mysql_old')->table('product_category')->get();

        if (isset($productCategories)) {

            foreach ($productCategories as $productCategory) {
                /**
                 * Migrate old to new database table
                 */
                ProductCategory::create([
                    'id' => $productCategory->id,
                    'name' => $productCategory->name,
                    'description' => $productCategory->description,
                    'product_id' => $productCategory->product_id,
                    'create_by' => Constant::DEFAULT_CREATE_BY,
                    'update_by' => Constant::DEFAULT_UPDATE_BY,
                    'status' => ($productCategory->status == Constant::DEFAULT_STATUS_ACTIVE) ? Constant::DEFAULT_STATUS_ACTIVE : Constant::DEFAULT_STATUS_DELETED
                ]);
            }

        }

    }
}
