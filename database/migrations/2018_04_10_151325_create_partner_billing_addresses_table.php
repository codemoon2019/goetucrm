<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerBillingAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_billing_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->string('country',100)->nullable();
            $table->text('address')->nullable();
            $table->string('city',100)->nullable();
            $table->string('state',100)->nullable();
            $table->string('zip',100)->nullable();
            $table->string('create_by',50);
            $table->string('update_by',50);
            $table->string('country_code',80)->nullable();
            $table->text('address2')->nullable();   
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
        Schema::dropIfExists('partner_billing_addresses');
    }
}
