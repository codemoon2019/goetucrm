<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerProductModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_product_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->integer('product_id');
            $table->integer('product_module_id');
            $table->string('name',255);
            $table->string('value',255);
            $table->string('type',255);
            $table->string('status',1);
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
        Schema::dropIfExists('partner_product_modules');
    }
}
