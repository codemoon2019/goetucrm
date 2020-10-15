<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_headers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type',10);
            $table->integer('parent_id');
            $table->integer('partner_id');
            $table->dateTime('date');
            $table->string('source',255);
            $table->string('target',255);
            $table->decimal('gross_amount',18,2);
            $table->decimal('discount',18,2);
            $table->decimal('net_amount',18,2);
            $table->string('status',10);
            $table->string('create_by',50);
            $table->string('update_by',50);
            $table->text('remarks');
            $table->string('return_type',255);
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
        Schema::dropIfExists('inventory_headers');
    }
}
