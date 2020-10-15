<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductOrderAddBatchId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('product_orders', 'batch_id')) {
            Schema::table('product_orders', function (Blueprint $table) {
                $table->string('batch_id')->default(0)->nullable();
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
        if (!Schema::hasColumn('product_orders', 'batch_id')) {
            Schema::table('product_orders', function (Blueprint $table) {
                $table->dropColumn('batch_id');
            }); 
        }
    }
}
