<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('product_order_details', 'price')) {
            Schema::table('product_order_details', function (Blueprint $table) {
                $table->decimal('price',18,2)->default(0.00)->nullable();
            }); 
        }

        if (!Schema::hasColumn('product_order_details', 'start_date')) {
            Schema::table('product_order_details', function (Blueprint $table) {
                $table->dateTime('start_date')->nullable();
            }); 
        }

        if (!Schema::hasColumn('product_order_details', 'end_date')) {
            Schema::table('product_order_details', function (Blueprint $table) {
                $table->dateTime('end_date')->nullable();
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
        if (!Schema::hasColumn('product_order_details', 'price')) {
            Schema::table('product_order_details', function (Blueprint $table) {
                $table->dropColumn('price');
            }); 
        }

        if (!Schema::hasColumn('product_order_details', 'start_date')) {
            Schema::table('product_order_details', function (Blueprint $table) {
                $table->dropColumn('start_date');
            }); 
        }

        if (!Schema::hasColumn('product_order_details', 'end_date')) {
            Schema::table('product_order_details', function (Blueprint $table) {
                $table->dropColumn('end_date');
            }); 
        }

    }
}
