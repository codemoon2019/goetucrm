<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditCardSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_card_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->integer('product_id');
            $table->decimal('amount',18,2);
            $table->dateTime('effective_date');
            $table->string('create_by',20);
            $table->string('update_by',20);
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
        Schema::dropIfExists('credit_card_sales');
    }
}
