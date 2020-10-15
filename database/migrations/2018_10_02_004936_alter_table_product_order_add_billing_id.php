<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProductOrderAddBillingId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('product_orders', 'billing_id')) {
            Schema::table('product_orders', function (Blueprint $table) {
                $table->string('billing_id',60)->nullable();
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
        if (Schema::hasColumn('product_orders', 'billing_id')) {
            Schema::table('product_orders', function (Blueprint $table) {
                $table->dropColumn('billing_id');
            });
        }
    }
}
