<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailOnQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_on_queues', function (Blueprint $table) {
            $table->increments('id');
            $table->text('subject');
            $table->text('body');
            $table->text('email_address');
            $table->string('create_by',20);
            $table->integer('is_sent');
            $table->dateTime('sent_date');
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
        Schema::dropIfExists('email_on_queues');
    }
}
