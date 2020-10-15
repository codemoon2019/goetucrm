<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('training_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('training_id')->nullable();
            $table->string('name',255)->nullable();
            $table->string('description',255)->nullable();
            $table->string('module_code',255)->nullable();
            $table->string('module_path',255)->nullable();
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('training_modules');
    }
}
