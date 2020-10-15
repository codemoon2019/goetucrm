<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmailOnQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_on_queues', function (Blueprint $table) {
            $table->text('subject')->nullable()->change();
            $table->text('body')->nullable()->change();
            //$table->string('email_address',200)->nullable()->change();
            $table->string('create_by',20)->nullable()->change();
            $table->integer('is_sent')->nullable()->change();
            $table->dateTime('sent_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_on_queues', function (Blueprint $table) {
            //
        });
    }
}
