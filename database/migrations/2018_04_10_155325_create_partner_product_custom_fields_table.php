<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerProductCustomFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_product_custom_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->integer('product_id');
            $table->integer('product_field_id');
            $table->text('value');
            $table->string('create_by',50);
            $table->string('update_by',50);
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
        Schema::dropIfExists('partner_product_custom_fields');
    }
}
