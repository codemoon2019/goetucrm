<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id');
            $table->integer('product_id');
            $table->integer('partner_id');
            $table->decimal('sales',18,2)->nullable();
            $table->decimal('withoutMarkUp',18,2)->nullable();
            $table->decimal('withoutMarkUpCommission',18,2)->nullable();
            $table->decimal('markUp',18,2)->nullable();
            $table->decimal('markUpCommission',18,2)->nullable();
            $table->decimal('totalCommission',18,2)->nullable();
            $table->integer('directUpline')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_commissions');
    }
}
