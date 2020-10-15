<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantProductApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_product_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_name',255);
            $table->integer('partner_id');
            $table->text('product_settings');
            $table->string('create_by',50);
            $table->string('update_by',50);
            $table->integer('sub_product_id');
            $table->integer('product_id');
            $table->integer('quantity');
            $table->string('sub_product_name',255);
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
        Schema::dropIfExists('merchant_product_applications');
    }
}
