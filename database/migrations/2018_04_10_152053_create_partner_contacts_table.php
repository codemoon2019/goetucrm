<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->string('first_name',100)->nullable();
            $table->string('last_name',100)->nullable();
            $table->string('middle_name',100)->nullable();
            $table->string('email',100)->nullable();
            $table->string('mobile_number',20)->nullable();
            $table->string('other_number',20)->nullable();
            $table->string('position',100)->nullable();
            $table->text('address1')->nullable();
            $table->text('address2')->nullable();
            $table->string('city',100)->nullable();
            $table->string('state',100)->nullable();
            $table->string('zip',100)->nullable();
            $table->string('country',100)->nullable();
            $table->string('create_by',50)->nullable();
            $table->string('update_by',50)->nullable();
            $table->string('extension',20)->nullable();
            $table->string('company_name',100)->nullable();
            $table->integer('is_original_contact')->nullable();
            $table->string('ssn',20)->nullable();
            $table->text('website')->nullable();
            $table->string('other_number_2',20)->nullable();
            $table->string('fax',20)->nullable();
            $table->string('email2',100)->nullable();
            $table->dateTime('dob')->nullable();
            $table->string('country_code',10)->nullable();
            $table->string('ownership_percentage',3)->nullable();
            $table->string('mobile_number_2',20)->nullable();
            $table->dateTime('business_acquired_date')->nullable();
            $table->string('issued_id',100)->nullable();
            $table->dateTime('id_exp_date')->nullable();
            $table->string('extension_2',20)->nullable();
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
        Schema::dropIfExists('partner_contacts');
    }
}
