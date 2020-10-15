<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEventIdInCalendarActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendar_activities', function (Blueprint $table) {
            $table->string('event_id', 500)->change();
            $table->renameColumn('event_id', 'google_event_id');
            $table->string('outlook_event_id',500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calendar_activities', function (Blueprint $table) {
            $table->dropColumn('google_event_id');
            $table->dropColumn('outlook_event_id');            
        });
    }
}
