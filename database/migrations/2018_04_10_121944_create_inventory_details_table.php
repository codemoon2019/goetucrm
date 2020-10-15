<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_details', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('line_number');
            $table->integer('product_id');
            $table->decimal('price',18,2);
            $table->decimal('discount',18,2);
            $table->decimal('quantity',18,2);
            $table->decimal('subtotal',18,2);
            $table->string('make',255);
            $table->string('model',255);
            $table->string('color',255);
            $table->string('serial_no',255);
            $table->string('r_serial_no',255);
            $table->integer('is_return');
            $table->integer('is_sold');
            $table->integer('ref_no');
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
        Schema::dropIfExists('inventory_details');
    }
}
