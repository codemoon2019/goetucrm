<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',10);
            $table->string('description',100);
            $table->string('intervals',100);
            $table->text('remarks');
            $table->integer('sequence');
            $table->string('create_by',20);
            $table->string('status',1);
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
        Schema::dropIfExists('calendar_reminders');
    }
}
