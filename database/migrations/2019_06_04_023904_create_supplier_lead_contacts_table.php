<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierLeadContactsTable extends Migration
{
    public function up()
    {
        Schema::create('supplier_lead_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('position')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('mobile')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_phone_2')->nullable();
            $table->string('fax')->nullable();

            $table->unsignedInteger('supplier_lead_id');
            $table->foreign('supplier_lead_id')
                ->references('id')
                ->on('supplier_leads');

            $table->string('status', 1)->default('A');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('supplier_lead_contacts');
        Schema::enableForeignKeyConstraints();
    }
}
