<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerPaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_payment_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->string('payment_type',10);
            $table->string('account_number',80);
            $table->string('routing_number',80);
            $table->string('status',1);
            $table->string('create_by',50);
            $table->string('update_by',50);
            $table->string('card_number',30);
            $table->string('cvv_code',10);
            $table->string('expiry_month',2);
            $table->string('expiry_year',4);
            $table->string('bank_name',300);
            $table->string('account_name',300);
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
        Schema::dropIfExists('partner_payment_methods');
    }
}
