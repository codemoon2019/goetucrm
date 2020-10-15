<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductsAddItemId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasColumn('products', 'item_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('item_id')->default('')->nullable();
            }); 
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        if (Schema::hasColumn('products', 'item_id')) {
            Schema::table('products', function (Blueprint $table) {
                  $table->dropColumn('item_id');
            }); 
        }
      
    }
}
