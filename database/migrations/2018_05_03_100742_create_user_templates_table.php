<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('resource_id')->unsigned();
            /*
             * Foreign key
             */
            $table->foreign('resource_id')->references('id')->on('resources')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->integer('user_type_id')->unsigned();
            /*
             * Foreign key
             */
            $table->foreign('user_type_id')->references('id')->on('user_types')
                ->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('user_templates');
    }
}
