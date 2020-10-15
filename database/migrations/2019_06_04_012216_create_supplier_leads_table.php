<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierLeadsTable extends Migration
{
    public function up()
    {
        Schema::create('supplier_leads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('doing_business_as');
            $table->string('business_name')->nullable();

            $table->string('business_address');
            $table->string('business_address_2')->nullable();
            $table->unsignedInteger('country_id');
            $table->foreign('country_id')
                ->references('id')
                ->on('countries');
            $table->string('state_id')
                ->foreign('state_id')
                ->references('id')
                ->on('states');
            $table->string('city');
            $table->string('zip');

            $table->string('business_phone');
            $table->string('extension')->nullable();
            $table->string('fax')->nullable();

            $table->string('business_phone_2')->nullable();
            $table->string('extension_2')->nullable();
            
            $table->string('business_email')->nullable();

            $table->unsignedInteger('partner_id')->nullable();
            $table->foreign('partner_id')
                ->references('id')
                ->on('partners');

            $table->string('status', 1)->default('A');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('supplier_leads');
        Schema::enableForeignKeyConstraints();
    }
}
