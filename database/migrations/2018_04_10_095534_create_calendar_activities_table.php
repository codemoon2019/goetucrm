<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('partner_id')->default(-1)->nullable();
            $table->integer('parent_id')->default(-1)->nullable();
            $table->smallInteger('remind_flag')->default(0)->nullable();
            $table->integer('type')->nullable();
            $table->string('title',200)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('start_time',20)->nullable();
            $table->string('end_time',20)->nullable();
            $table->text('agenda')->nullable();
            $table->string('time_zone',100)->nullable();
            $table->string('reminder',2)->nullable();
            $table->text('attendees')->nullable();
            $table->string('location',200)->nullable();
            $table->string('frequency',2)->nullable();
            $table->string('calendar_status',2)->nullable();
            $table->string('status',2)->nullable();
            $table->string('create_by',20)->nullable();
            $table->string('update_by',20)->nullable();
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
        Schema::dropIfExists('calendar_activities');
    }
}
