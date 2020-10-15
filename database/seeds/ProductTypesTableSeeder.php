<?php

use Illuminate\Database\Seeder;
use App\Models\ProductType;
use Illuminate\Support\Facades\DB;
use App\Contracts\Constant;

class ProductTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Get product types from old database
         */
        $productTypes = DB::connection('mysql_old')->table('product_types')->get();
        /**
         * Check if there's data from the old database
         */
        if (isset($productTypes)) {
            /**
             * Loop product types
             */
            foreach ($productTypes as $productType) {
                /**
                 * Migrate product types from old database
                 */
                ProductType::create([
                    'id' => $productType->id,
                    'code' => $productType->code,
                    'description' => $productType->description,
                    'status' => ProductType::PRODUCT_TYPE_STATUS_ACTIVE,
                    'create_by' => Constant::DEFAULT_CREATE_BY,
                    'update_by' => Constant::DEFAULT_UPDATE_BY
                ]);
            }
        }

    }
}
