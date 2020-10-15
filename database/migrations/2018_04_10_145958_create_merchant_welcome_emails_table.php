<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantWelcomeEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_welcome_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->integer('order_id');
            $table->integer('product_id');
            $table->text('email_address');
            $table->string('name',100);
            $table->text('description');
            $table->string('create_by',50);
            $table->string('update_by',50);
            $table->string('status',1);
            $table->integer('is_sent');
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
        Schema::dropIfExists('merchant_welcome_emails');
    }
}
