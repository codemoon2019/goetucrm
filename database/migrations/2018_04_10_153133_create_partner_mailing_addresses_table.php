<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerMailingAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_mailing_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->text('address');
            $table->text('address2')->nullable();
            $table->string('city',100)->nullable();
            $table->string('state',100)->nullable();
            $table->string('zip',100)->nullable();
            $table->string('country',100)->nullable();
            $table->string('country_code',10)->nullable();
            $table->string('create_by',50)->nullable();
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
        Schema::dropIfExists('partner_mailing_addresses');
    }
}
