<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerPaymentInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_payment_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->integer('payment_type_id');
            $table->integer('is_default_payment')->nullable();
            $table->string('bank_name',50)->nullable();
            $table->string('routing_number',50)->nullable();
            $table->string('bank_account_number',50)->nullable();
            $table->string('cardholder_name',100)->nullable();
            $table->string('credit_card_no',50)->nullable();
            $table->string('ccv',10)->nullable();
            $table->string('expiration_date',10)->nullable();
            $table->string('address1',100)->nullable();
            $table->string('address2',100)->nullable();
            $table->string('city',100)->nullable();
            $table->string('state',100)->nullable();
            $table->string('zip',100)->nullable();
            $table->string('create_by',50)->nullable();
            $table->string('update_by',50)->nullable();
            $table->string('status',1)->nullable();
            $table->string('sftp_address',50)->nullable();
            $table->string('sftp_user',50)->nullable();
            $table->string('sftp_password',50)->nullable();
            $table->string('pay_to',100)->nullable();
            $table->string('pay_token',100)->nullable();
            $table->text('email_address')->nullable();
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
        Schema::dropIfExists('partner_payment_infos');
    }
}
