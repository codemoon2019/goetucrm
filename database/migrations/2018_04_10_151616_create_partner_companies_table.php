<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_companies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id');
            $table->text('company_name')->nullable();
            $table->text('address1')->nullable();
            $table->text('address2')->nullable();
            $table->string('city',100)->nullable();
            $table->string('state',100)->nullable();
            $table->string('zip',100)->nullable();
            $table->string('country',100)->nullable();
            $table->string('email',100)->nullable();
            $table->string('phone1',20)->nullable();
            $table->string('phone2',20)->nullable();
            $table->string('update_by',50)->nullable();
            $table->string('dba',80)->nullable();
            $table->string('fax',20)->nullable();
            $table->string('mobile_number',20)->nullable();
            $table->string('ownership',100)->nullable();
            $table->string('country_code',10)->nullable();
            $table->string('website',200)->nullable();
            $table->string('ssn',20)->nullable();
            $table->string('business_date',20)->nullable();
            $table->string('extension',20)->nullable();
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
        Schema::dropIfExists('partner_companies');
    }
}
