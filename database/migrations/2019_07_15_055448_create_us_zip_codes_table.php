<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsZipCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('us_zip_codes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('zip_code', 5);
            $table->string('type', 10);
            $table->string('city', 50)->nullable();

            $table->integer('state_id')->unsigned();
            $table->foreign('state_id')->references('id')->on('states');

            $table->integer('country_id')->unsigned();
            $table->foreign('country_id')->references('id')->on('countries');

            $table->smallInteger('is_primary_city')->default(0);
            $table->smallInteger('is_acceptable_city')->default(0);

            $table->string('county', 50)->nullable();
            
            $table->timestamps();
        });

        // Artisan::call('db:seed', [
        //     '--class' => AdditionalUsStateTableSeeder::class
        // ]);

        // Artisan::call('db:seed', [
        //     '--class' => UsZipCodesTableSeeder::class
        // ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('us_zip_codes');
    }
}
