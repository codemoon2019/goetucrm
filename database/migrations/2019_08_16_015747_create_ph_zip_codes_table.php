<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhZipCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ph_zip_codes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('zip_code', 4);
            $table->string('type', 10)->nullable();
            $table->string('city', 500)->nullable();

            $table->integer('state_id')->unsigned();
            $table->foreign('state_id')->references('id')->on('states');

            $table->integer('country_id')->unsigned();
            $table->foreign('country_id')->references('id')->on('countries');

            $table->smallInteger('is_primary_city')->default(0);
            $table->smallInteger('is_acceptable_city')->default(0);

            $table->string('county', 50)->nullable();

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
        Schema::dropIfExists('ph_zip_codes');
    }
}
