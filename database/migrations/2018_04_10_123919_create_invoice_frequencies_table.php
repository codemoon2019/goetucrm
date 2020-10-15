<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceFrequenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_frequencies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->integer('partner_id');
            $table->integer('product_id');
            $table->string('frequency',50)->nullable();
            $table->dateTime('register_date')->nullable();
            $table->dateTime('bill_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->dateTime('last_bill_date')->nullable();
            $table->decimal('amount',18,2)->nullable();
            $table->string('status',20)->nullable();
            $table->text('remarks')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('update_by',50)->nullable();
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
        Schema::dropIfExists('invoice_frequencies');
    }
}
